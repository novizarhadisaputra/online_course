<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public static function check(object $data, array $roles = ['customer']): User
    {
        $user = UserService::findUserByEmail(
            email: $data->email,
            roles: $roles
        );
        if (!Hash::check($data->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => trans('validation.exists', ['attribute' => 'email']),
                'password' => trans('validation.exists', ['attribute' => 'password'])
            ]);
        }

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
}
