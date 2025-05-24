<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class UserService
{

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function findById(
        string $id,
        array $fields = ['*'],
        array $roles = [],
        array $relationships = ['roles'],
    ): User | null {
        $user = User::select($fields)
            ->with($relationships)
            ->where('id', $id);
        $user = $user->whereHas('roles', fn(Builder $q) => $q->whereIn('name', $roles));
        $user = $user->first();
        return $user;
    }

    public static function findByEmail(
        string $email,
        array $fields = ['*'],
        array $roles = ['customer'],
        array $relationships = ['roles'],
    ): User | null {
        $user = User::select($fields)
            ->with($relationships)
            ->where('email', $email);
        $user = $user->whereHas('roles', fn(Builder $q) => $q->whereIn('name', $roles));
        $user = $user->first();
        return $user;
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
