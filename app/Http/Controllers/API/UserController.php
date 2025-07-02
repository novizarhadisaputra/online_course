<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\AddressResource;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\InstructorResource;
use App\Http\Resources\CertificateResource;
use App\Http\Resources\NotificationResource;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\User\StoreAddressRequest;
use App\Http\Requests\User\UpdateAvatarRequest;
use App\Http\Requests\User\UpdateAddressRequest;

class UserController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function reviews(Request $request, string $id)
    {
        try {
            if ($id != $request->user()->id) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'id'])]);
            }
            $reviews = $request->user()->reviews()->paginate($request->input('limit', 10));
            return $this->success(data: ReviewResource::collection($reviews), paginate: $reviews);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function addresses(Request $request, string $id)
    {
        try {
            if ($id != $request->user()->id) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'id'])]);
            }
            $addresses = $request->user()->addresses()->paginate($request->input('limit', 10));
            return $this->success(data: AddressResource::collection($addresses), paginate: $addresses);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function certificates(Request $request, string $id)
    {
        try {
            if ($id != $request->user()->id) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'id'])]);
            }
            $certificates = $request->user()->certificates()->paginate($request->input('limit', 10));
            return $this->success(data: CertificateResource::collection($certificates), paginate: $certificates);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function following(Request $request, string $id)
    {
        try {
            if ($id != $request->user()->id) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'id'])]);
            }
            $following = $request->user()->following()->paginate($request->input('limit', 10));
            return $this->success(data: InstructorResource::collection($following), paginate: $following);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function followers(Request $request, string $id)
    {
        try {
            if ($id != $request->user()->id) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'id'])]);
            }
            $followers = $request->user()->followers()->paginate($request->input('limit', 10));
            return $this->success(data: InstructorResource::collection($followers), paginate: $followers);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function notifications(Request $request, string $id)
    {
        try {
            if ($id != $request->user()->id) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'id'])]);
            }
            $notifications = $request->user()->notifications()->paginate($request->input('limit', 10));
            return $this->success(data: NotificationResource::collection($notifications), paginate: $notifications);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeAddress(StoreAddressRequest $request, string $id)
    {
        try {
            if ($request->user()->id != $id) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'user id'])]);
            }
            $address = $request->user()->addresses()->create([
                "label" => $request->label,
                "first_name" => $request->first_name,
                "last_name" => $request->last_name,
                "email" => $request->user()->email,
                "phone" => $request->phone,
                "street_line1" => $request->street_line1,
                "street_line2" => $request->street_line2,
                "country" => $request->country,
                "province_id" => $request->province_id,
                "regency_id" => $request->regency_id,
                "district_id" => $request->district_id,
                "village_id" => $request->village_id,
                "postal_code" => $request->postal_code,
            ]);

            if ($address->status) {
                $request->user()->addresses()->where('id', '<>', $address->id)->update([
                    'status' => false,
                ]);
            }

            $note = $address->note()->where('user_id', $request->user()->id)->first();
            if (!$note) {
                $address->note()->create([
                    'name' => $request->label,
                    'description' => $request->note,
                    'user_id' => $request->user()->id,
                    'status' => true,
                ]);
            } else {
                $note->name = $request->label;
                $note->description = $request->note;
                $note->save();
            }
            return $this->success(data: new AddressResource($address));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     */
    public function showEmail(Request $request, string $id)
    {
        try {
            if ($request->user()->id != $id) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'user id'])]);
            }
            return $this->success(data: ['email' => $request->user()->email]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function showAddress(Request $request, string $id, string $address_id)
    {
        try {
            if ($request->user()->id != $id || $request->user()->id != $request->id) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'user id'])]);
            }

            $user = UserService::findUserById($id);

            $address = $user->addresses()->where('id', $address_id)->first();
            if (!$address) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'address id'])]);
            }

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
        DB::beginTransaction();
        try {
            if ($request->user()->id != $id) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'user id'])]);
            }
            $user = UserService::findUserById($id);
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
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateAvatar(UpdateAvatarRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            if ($request->user()->id != $id || $request->user()->id != $request->id) {
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
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateAddress(UpdateAddressRequest $request, string $id, string $address_id)
    {
        DB::beginTransaction();
        try {
            if ($request->user()->id != $id) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'user id'])]);
            }

            $user = UserService::findUserById($id);

            $address = $user->addresses()->where('id', $address_id)->first();
            if (!$address) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'address id'])]);
            }

            $address->label = $request->label ?? $address->label;
            $address->status = $request->status;

            if ($address->status) {
                $user->addresses()->where('id', '<>', $address_id)->update([
                    'status' => false,
                ]);
            }
            $address->first_name = $request->first_name ?? $address->first_name;
            $address->last_name = $request->last_name ?? $address->last_name;
            $address->email = $request->user()->email;
            $address->phone = $request->phone ?? $address->phone;
            $address->street_line1 = $request->street_line1;
            $address->street_line2 = $request->street_line2;
            $address->country = $request->country;
            $address->province_id = $request->province_id;
            $address->regency_id = $request->regency_id;
            $address->district_id = $request->district_id;
            $address->village_id = $request->village_id;
            $address->postal_code = $request->postal_code;
            $address->save();

            $note = $address->note()->where('user_id', $request->user()->id)->first();
            if (!$note) {
                $address->note()->create([
                    'name' => $request->label,
                    'description' => $request->note,
                    'user_id' => $request->user()->id,
                    'status' => true,
                ]);
            } else {
                $note->name = $request->label;
                $note->description = $request->note;
                $note->user_id = $request->user()->id;
                $note->save();
            }

            DB::commit();
            return $this->success(data: new AddressResource($address));
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function destroyAddress(Request $request, string $id, string $address_id)
    {
        DB::beginTransaction();
        try {
            if ($request->user()->id != $id || $request->user()->id != $request->id) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'user id'])]);
            }

            $user = UserService::findUserById($id);

            $address = $user->addresses()->where('id', $address_id)->first();
            if (!$address) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'address id'])]);
            }
            $address->note()->where('user_id', $request->user()->id)->delete();
            $address->delete();

            DB::commit();
            return $this->success(data: null);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
