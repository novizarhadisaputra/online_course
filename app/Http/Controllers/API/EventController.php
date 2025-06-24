<?php

namespace App\Http\Controllers\API;

use App\Models\Event;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $events = Event::with(['viewers'])->active();
            if ($request->search) {
                $events = $events->where('name', 'ilike', "%$request->search%");
            }
            if ($request->filter) {
                if (isset($request->filter['prices'])) {
                    if (Str::contains(implode(' ', $request->filter['prices']), ['paid', 'free'])) {
                        $events = $events->where('is_paid', '<>', null);
                    } else {
                        $is_paid = Str::contains(implode(' ', $request->filter['prices']), ['paid']);
                        $events = $events->where('is_paid', $is_paid);
                    }
                }
                if (isset($request->filter['categories'])) {
                    $events = $events->whereHas('category', fn(Builder $q) => $q->whereIn('name', $request->filter['categories']));
                }
            }

            $start = now()->startOfMonth();
            $end = now()->endOfMonth();

            $events = $events->whereBetween('start_time', [$start, $end])->paginate($request->input('limit', 10));
            return $this->success(data: Event::collection($events), paginate: $events);
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
    public function show(Request $request, string $slug)
    {
        try {
            $event = Event::where('slug', $slug)->first();
            if (!$event) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'event id'])]);
            }
            if ($request->user()) {
                $viewer = $event->viewers()
                    ->whereDate('created_at', Carbon::today())
                    ->where('user_id', $request->user()->id)
                    ->first();
                if ($viewer) {
                    $event->viewers()->create([
                        'user_id' => $request->user()->id
                    ]);
                }
            }
            return $this->success(data: new EventResource($event));
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
