<?php

namespace App\Http\Controllers\API;

use App\Models\Regency;
use App\Models\Province;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\DistrictResource;
use App\Http\Resources\RegencyResource;
use App\Http\Resources\ProvinceResource;
use App\Http\Resources\VillageResource;
use App\Models\District;
use Illuminate\Validation\ValidationException;

class AreaController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function provinces(Request $request)
    {
        try {
            $provinces = Province::select(['*']);
            if ($request->search) {
                $provinces = $provinces->where('name', 'ilike', "%$request->search%");
            }
            $provinces = $provinces->paginate($request->input('limit', 10));
            return $this->success(data: ProvinceResource::collection($provinces), paginate: $provinces);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function regencies(Request $request, string $province_id)
    {
        try {
            $province = Province::find($province_id);
            if (!$province) {
                throw ValidationException::withMessages(['province_id', trans('validation.exists', ['attribute' => 'province'])]);
            }
            $regencies = $province->regencies()->select(['*']);
            if ($request->search) {
                $regencies = $regencies->where('regencies.name', 'ilike', "%$request->search%");
            }
            $regencies = $regencies->paginate($request->input('limit', 10));
            return $this->success(data: RegencyResource::collection($regencies), paginate: $regencies);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function districts(Request $request, string $province_id, string $regency_id)
    {
        try {
            $province = Province::find($province_id);
            if (!$province) {
                throw ValidationException::withMessages(['province_id', trans('validation.exists', ['attribute' => 'province'])]);
            }
            $regency = $province->regencies()->where('id', $regency_id)->first();
            if (!$regency) {
                throw ValidationException::withMessages(['regency_id', trans('validation.exists', ['attribute' => 'regency'])]);
            }
            $districts = $regency->districts()->select(['*']);
            if ($request->search) {
                $districts = $districts->where('districts.name', 'ilike', "%$request->search%");
            }
            $districts = $districts->paginate($request->input('limit', 10));
            return $this->success(data: DistrictResource::collection($districts), paginate: $districts);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function villages(Request $request, string $province_id, string $regency_id, string $district_id)
    {
        try {
            $province = Province::find($province_id);
            if (!$province) {
                throw ValidationException::withMessages(['province_id', trans('validation.exists', ['attribute' => 'province'])]);
            }
            $regency = $province->regencies()->where('id', $regency_id)->first();
            if (!$regency) {
                throw ValidationException::withMessages(['regency_id', trans('validation.exists', ['attribute' => 'regency'])]);
            }
            $district = $regency->districts()->where('id', $district_id)->first();
            if (!$district) {
                throw ValidationException::withMessages(['district_id', trans('validation.exists', ['attribute' => 'district'])]);
            }
            $villages = $district->villages()->select(['*']);
            if ($request->search) {
                $villages = $villages->where('villages.name', 'ilike', "%$request->search%");
            }
            $villages = $villages->paginate($request->input('limit', 10));
            return $this->success(data: VillageResource::collection($villages), paginate: $villages);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
