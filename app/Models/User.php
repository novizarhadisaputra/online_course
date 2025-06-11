<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use App\Models\Like;
use App\Models\News;
use App\Models\Branch;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\Follow;
use App\Models\Lesson;
use App\Models\Review;
use App\Models\Address;
use App\Models\Progress;
use App\Models\Couponable;
use App\Models\Transaction;
use App\Models\ModelHasBranch;
use Illuminate\Support\Carbon;
use App\Models\TransactionDetail;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\HasTenants;
use Filament\Models\Contracts\FilamentUser;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Yebor974\Filament\RenewPassword\Traits\RenewPassword;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Yebor974\Filament\RenewPassword\Contracts\RenewPasswordContract;

class User extends Authenticatable implements FilamentUser, HasMedia, HasAvatar, HasTenants, RenewPasswordContract
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids, HasApiTokens, HasRoles;
    use InteractsWithMedia, RenewPassword;

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

    /**
     * Specifies the user's FCM tokens
     *
     * @return string|array
     */
    public function routeNotificationForFcm(): array
    {
        return $this->deviceTokens()->select(['token'])->pluck('token')->toArray();
    }

    /**
     * Get all of the deviceTokens for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deviceTokens(): HasMany
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function branches(): MorphToMany
    {
        return $this->morphToMany(Branch::class, 'model', ModelHasBranch::class);
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->branches;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->branches()->whereKey($tenant)->exists();
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->hasMedia('avatars') ? $this->getMedia('avatars')->first()->getTemporaryUrl(Carbon::now()->addHour()) : null;
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
        return $this->morphMany(Address::class, 'model');
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
     * Get all of the like lesson that are assigned this user.
     */

    public function likeLessons(): MorphToMany
    {
        return $this->morphedByMany(Lesson::class, 'likeable', Like::class);
    }

    /**
     * Get all of the following that are assigned this user.
     */

    public function following(): MorphToMany
    {
        return $this->morphedByMany(self::class, 'model', Follow::class);
    }

    /**
     * Get all of the followers for the instructor.
     */
    public function followers(): MorphToMany
    {
        return $this->morphToMany(self::class, 'model', Follow::class);
    }

    /**
     * Get all of the coupons for the users.
     */
    public function coupons(): MorphToMany
    {
        return $this->morphToMany(Coupon::class, 'model', Couponable::class);
    }

    /**
     * Get all of the progress courses that are assigned this user.
     */

    public function progressCourses(): MorphToMany
    {
        return $this->morphedByMany(Course::class, 'model', Progress::class)
            ->withPivot(['id', 'data', 'status']);
    }

    /**
     * Get all of the enrollment courses that are assigned this user.
     */
    public function enrollmentCourses(): MorphToMany
    {
        return $this->morphedByMany(Course::class, 'model', Enrollment::class);
    }
}
