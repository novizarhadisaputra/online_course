<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class AuthService
{

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function check(object $data, array $roles = ['customer']): User
    {
        $user = self::findUserByEmail(email: $data->email, roles: $roles);
        if (!Hash::check($data->password, $user->password)) {
            throw  ValidationException::withMessages([
                'email' => trans('validation.exists', ['attribute' => 'email']),
                'password' => trans('validation.exists', ['attribute' => 'password'])
            ]);
        }

        return $user;
    }

    public static function findById(
        string $id,
        array $fields = ['*'],
        array $roles = [],
    ): User | null {
        $user = User::select($fields)->where('id', $id);
        $user = $user->whereHas('roles', fn(Builder $q) => $q->whereIn('name', $roles));
        $user = $user->first();
        return $user;
    }

    public static function findByEmail(
        string $email,
        array $fields = ['*'],
        array $roles = [],
    ): User | null {
        $user = User::select($fields)->where('email', $email);
        $user = $user->whereHas('roles', fn(Builder $q) => $q->whereIn('name', $roles));
        $user = $user->first();
        return $user;
    }

    public static function isEmailVerified(User $user): bool
    {
        return $user->email_verified_at ? true : false;
    }

    public static function checkEmailVerified(User $user): void
    {
        if (!self::isEmailVerified($user)) {
            throw  ValidationException::withMessages([
                'email' => trans('validation.verify', ['attribute' => 'email']),
            ]);
        }
    }

    public static function findUserByEmail(string $email, array $roles = ['customer']): User
    {
        $user = self::findByEmail(email: $email, roles: $roles);
        if (!$user) {
            throw  ValidationException::withMessages([
                'email' => trans('validation.exists', ['attribute' => 'email']),
            ]);
        }
        return $user;
    }

    public static function findUserById(string $id, array $roles = ['customer']): User
    {
        $user = self::findById(id: $id, roles: $roles);
        if (!$user) {
            throw  ValidationException::withMessages([
                'id' => trans('validation.exists', ['attribute' => 'id']),
            ]);
        }
        return $user;
    }
}
