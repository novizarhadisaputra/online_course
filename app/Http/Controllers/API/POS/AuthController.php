<?php

namespace App\Http\Controllers\API\POS;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\POS\Auth\LoginRequest;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ResponseTrait;

    public function __construct() {}

    public function login(LoginRequest $request)
    {
        try {
            $user = AuthService::check($request, ['cashier']);
            AuthService::checkEmailVerified($user);

            $exists = $user->branches()->where('code', $request->branch_code)->exists();
            if (!$exists) {
                throw ValidationException::withMessages(['branch_code' => trans('validation.exists', ['attribute' => 'branch code'])]);
            }

            $data = (object) [
                'token' => $user->createToken('auth_token')->plainTextToken,
                'user' => new UserResource($user),
            ];
            return $this->success(message: 'Ok', status: 200, data: $data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // Profile
    public function profile(Request $request)
    {
        return $this->success(data: new UserResource($request->user()));
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->success(message: 'Logged out successfully');
    }
}
