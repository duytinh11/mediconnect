<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorCardResource;
use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorSearchController extends Controller
{
    /**
     * List doctors with optional filters (city, specialty, keyword).
     */
    public function index(Request $request)
    {
        $doctors = Doctor::with(['user', 'city'])
            ->when($request->filled('city_id'), fn ($q) => $q->where('city_id', $request->integer('city_id')))
            ->when($request->filled('specialty'), fn ($q) => $q->where('specialty', 'like', '%' . $request->specialty . '%'))
            ->when($request->filled('keyword'), function ($q) use ($request) {
                $keyword = $request->keyword;
                $q->whereHas('user', fn ($sub) => $sub->where('name', 'like', "%{$keyword}%"));
            })
            ->whereHas('user', fn ($q) => $q->where('status', 'active'))
            ->orderByDesc('id')
            ->paginate($request->integer('per_page', 15));

        return DoctorCardResource::collection($doctors);
    }

    /**
     * Show a single doctor with relationships.
     */
    public function show(Doctor $doctor)
    {
        $doctor->load(['user', 'city']);
        return new DoctorCardResource($doctor);
    }
}
