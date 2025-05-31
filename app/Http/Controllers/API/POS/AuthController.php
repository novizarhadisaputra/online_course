<?php

namespace App\Http\Controllers\API\POS;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\POS\Auth\LoginRequest;
use App\Models\Branch;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ResponseTrait;

    public function __construct() {}

    public function login(LoginRequest $request)
    {
        try {
            $branch = Branch::where('code', $request->branch_code)->select(['id', 'code'])->first();
            if (!$branch) {
                throw ValidationException::withMessages(['branch_code' => trans('validation.exists', ['attribute' => 'branch code'])]);
            }
            $user = AuthService::check($request, ['cashier']);
            AuthService::checkEmailVerified($user);

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
}
