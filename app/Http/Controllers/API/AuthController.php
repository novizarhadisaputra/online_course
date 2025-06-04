<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Jobs\SendVerificationEmailJob;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResendVerifyEmailRequest;
use App\Services\AuthService;
use App\Services\UserService;
use Exception;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ResponseTrait;

    public function __construct(protected AuthService $auth) {}

    public function register(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->first_name . ($request->last_name ? ' ' . $request->last_name : ''),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name ?? '',
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole(['customer']);

            $token = $user->createToken('auth_token')->plainTextToken;

            // Send verification email
            SendVerificationEmailJob::dispatch($user);

            $response = (object) [
                'token' => $token,
                'user' => new UserResource($user),
            ];

            DB::commit();

            return $this->success(data: $response, status: 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $user = AuthService::check($request);
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

    // Logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->success(message: 'Logged out successfully');
    }

    // Verify Email
    public function verifyEmail($id)
    {
        try {
            DB::beginTransaction();

            $user = UserService::findUserById($id);
            if (AuthService::isEmailVerified($user)) {
                throw  ValidationException::withMessages([
                    'email' => trans('validation.has_verify', ['attribute' => 'email']),
                ]);
            }

            $user->email_verified_at = now();
            $user->save();

            DB::commit();

            return redirect()->away(env('APP_URL_WEBSITE', 'localhost:3000'));
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    // Resend Verification Email
    public function resendVerifyEmail(ResendVerifyEmailRequest $request)
    {
        try {
            $user = UserService::findUserByEmail($request->email);
            if (AuthService::isEmailVerified($user)) {
                return $this->success(message: 'Email already verified', status: 200);
            }

            // Send verification email
            SendVerificationEmailJob::dispatch($user);

            return $this->success(message: 'Verification email resent', status: 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // Forgot Password
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? $this->success(status: 200, message: 'Reset link sent to your email')
            : $this->error(message: 'Unable to send reset link');
    }

    // Reset Password
    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? $this->success(status: 200, message: 'Password reset successfully')
            : $this->error(message: 'Invalid token');
    }
}
