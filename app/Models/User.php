<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Panel;
use App\Models\Like;
use App\Models\Course;
use App\Models\Review;
use App\Models\Address;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\MediaLibrary\Conversions\ImageGenerators\Video;

class User extends Authenticatable implements FilamentUser, HasMedia, HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids, HasApiTokens, HasRoles;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'phone',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasVerifiedEmail();
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }

    /**
     * Get all of the transactions for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function transactions(): HasManyThrough
    {
        return $this->hasManyThrough(TransactionDetail::class, Transaction::class);
    }

    /**
     * Get all of the reviews for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function reviews(): HasManyThrough
    {
        return $this->hasManyThrough(Transaction::class, Review::class);
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    /**
     * Get all of the like course that are assigned this user.
     */

    public function likeCourses(): MorphToMany
    {
        return $this->morphedByMany(Course::class, 'likeable', Like::class);
    }

    /**
     * Get all of the like news that are assigned this user.
     */

    public function likeNews(): MorphToMany
    {
        return $this->morphedByMany(News::class, 'likeable', Like::class);
    }

    /**
     * Get all of the followers that are assigned this user.
     */

    public function followers(): MorphToMany
    {
        return $this->morphedByMany(self::class, 'followable', Follow::class);
    }

    /**
     * Get all of the following for the instructor.
     */
    public function following(): MorphToMany
    {
        return $this->morphToMany(self::class, 'followable', Follow::class);
    }
}
