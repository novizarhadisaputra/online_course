<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Services\UserService;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreAddressRequest;
use App\Http\Requests\User\UpdateAddressRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\InstructorResource;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\User\UpdateAvatarRequest;
use App\Http\Resources\AddressResource;

class UserController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function addresses(Request $request)
    {
        try {
            $addresses = $request->user()->addresses()->paginate($request->input('limit', 10));
            return $this->success(data: AddressResource::collection($addresses), paginate: $addresses);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function following(Request $request)
    {
        try {
            $following = $request->user()->following()->paginate($request->input('limit', 10));
            return $this->success(data: InstructorResource::collection($following), paginate: $following);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function followers(Request $request)
    {
        try {
            $followers = $request->user()->followers()->paginate($request->input('limit', 10));
            return $this->success(data: InstructorResource::collection($followers), paginate: $followers);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeAddress(StoreAddressRequest $request, string $id)
    {
        try {
            if ($request->user()->id !== $id) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'user id'])]);
            }
            $address = $request->user()->addresses()->create($request->validated());
            return $this->success(data: new AddressResource($address));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function showAddress(Request $request, string $id, string $address_id)
    {
        try {
            DB::beginTransaction();
            if ($request->user()->id !== $id || $request->user()->id !== $request->id) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'user id'])]);
            }

            $user = UserService::findUserById($id);

            $address = $user->addresses()->where('id', $address_id)->first();
            if (!$address) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'address id'])]);
            }

            DB::commit();
            return $this->success(data: new AddressResource($address));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id)
    {
        try {
            DB::beginTransaction();
            if ($request->user()->id !== $id) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'user id'])]);
            }
            $user = AuthService::findUserById($id);
            if ($request->hasFile('avatar')) {
                $user->clearMediaCollection('avatars');
                $user->addMediaFromRequest('avatar')->toMediaCollection('avatars');
            }

            $user->name = '';
            $user->first_name = $request->first_name ?? $user->first_name;
            $user->last_name = $request->last_name ?? $user->last_name;
            $user->description = $request->description ?? $user->description;
            $user->email = $request->email ?? $user->email;
            $user->phone = $request->phone ?? $user->phone;
            $user->password = $request->password ? Hash::make($request->password) : $user->password;
            $user->gender = $request->gender ?? $user->gender;
            $user->save();

            DB::commit();
            return $this->success(data: new UserResource($user));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateAvatar(UpdateAvatarRequest $request, string $id)
    {
        try {
            DB::beginTransaction();
            if ($request->user()->id !== $id || $request->user()->id !== $request->id) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'user id'])]);
            }
            $user = UserService::findUserById($id);

            if ($request->hasFile('avatar')) {
                $user->clearMediaCollection('avatars');
                $user->addMediaFromRequest('avatar')
                    ->usingFileName($request->user()->id . '.png')
                    ->toMediaCollection('avatars', 's3');
            }
            $user->save();

            DB::commit();
            return $this->success(data: new UserResource($user));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateAddress(UpdateAddressRequest $request, string $id, string $address_id)
    {
        try {
            DB::beginTransaction();
            if ($request->user()->id !== $id || $request->user()->id !== $request->id) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'user id'])]);
            }

            $user = UserService::findUserById($id);

            $address = $user->addresses()->where('id', $address_id)->first();
            if (!$address) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'address id'])]);
            }

            $address->first_name = $request->first_name ?? $address->first_name;
            $address->last_name = $request->last_name ?? $address->last_name;
            $address->email = $request->email ?? $address->email;
            $address->country = $request->country;
            $address->street_line1 = $request->street_line1;
            $address->street_line2 = $request->street_line2;
            $address->city = $request->city;
            $address->province = $request->province;
            $address->state = $request->state;
            $address->postal_code = $request->postal_code;
            $address->save();

            DB::commit();
            return $this->success(data: new AddressResource($address));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function destroyAddress(Request $request, string $id, string $address_id)
    {
        try {
            DB::beginTransaction();
            if ($request->user()->id !== $id || $request->user()->id !== $request->id) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'user id'])]);
            }

            $user = UserService::findUserById($id);

            $address = $user->addresses()->where('id', $address_id)->first();
            if (!$address) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'address id'])]);
            }

            $address->delete();

            DB::commit();
            return $this->success(data: null);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
