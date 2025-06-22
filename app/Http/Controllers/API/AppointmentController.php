<?php

namespace App\Http\Controllers\API;

use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use Illuminate\Validation\ValidationException;

class AppointmentController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $start = now()->startOfMonth();
            $end = now()->endOfMonth();
            $appointments = Appointment::where('user_id', $request->user()->id)
                ->whereBetween('date', [$start, $end])
                ->get();
            return $this->success(data: AppointmentResource::collection($appointments));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            $appointment = Appointment::where(['user_id' => $request->user()->id, 'id' => $id])->first();
            if (!$appointment) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'appointment id'])]);
            }
            return $this->success(data: new AppointmentResource($appointment));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
