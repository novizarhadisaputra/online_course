<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateAvatarRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\InstructorResource;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
            if ($request->user()->id !== $id || $request->user()->id !== $request->id) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'user id'])]);
            }
            $user = User::find($id);
            if (!$user) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'user id'])]);
            }
            if ($request->hasFile('avatar')) {
                $user->clearMediaCollection('avatars');
                $user->addMediaFromRequest('avatar')->toMediaCollection('avatars');
            }
            $user->name = $request->name;
            $user->description = $request->description;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = $request->password;
            $user->gender = $request->gender;

            $user->save();

            DB::commit();
            return $this->success(data: $user);
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
            $user = User::find($id);
            if (!$user) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'user id'])]);
            }
            if ($request->hasFile('avatar')) {
                $user->clearMediaCollection('avatars');
                $user->addMediaFromRequest('avatar')->toMediaCollection('avatars');
            }
            $user->save();

            DB::commit();
            return $this->success(data: $user);
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
}
