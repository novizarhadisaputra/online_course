<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Requests\Google\LoginRequest;
use Illuminate\Support\Facades\Hash;

class GoogleAuthController extends Controller
{
    use ResponseTrait;

    public function login(LoginRequest $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->userFromToken($request->access_token);
            $user = User::where('email', $googleUser->getEmail())->first();
            if (!$user) {
                $user = User::create([
                    'email' => $googleUser->getEmail(),
                    'name' => $googleUser->getName(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(16)),
                ]);

                $user->markEmailAsVerified();
            }
            $user->google_id = $googleUser->getId();
            $user->save();

            $data = (object) [
                'token' => $user->createToken('auth_token')->plainTextToken,
                'user' => new UserResource($user),
            ];
            return $this->success(message: 'Ok', status: 200, data: $data);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
