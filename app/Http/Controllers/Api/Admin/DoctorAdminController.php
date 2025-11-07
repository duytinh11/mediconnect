<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDoctorRequest;
use App\Http\Requests\Admin\UpdateDoctorRequest;
use App\Http\Resources\DoctorCardResource;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DoctorAdminController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with(['user', 'city'])->latest()->paginate();
        return DoctorCardResource::collection($doctors);
    }

    public function store(StoreDoctorRequest $request)
    {
        $doctor = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => strtolower($request->email),
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'doctor',
                'status' => $request->status ?? 'active',
            ]);

            $doctorData = Arr::only($request->validated(), [
                'city_id',
                'specialty',
                'license_number',
                'degrees',
                'bio',
                'available_slots',
                'status',
            ]);

            return $user->doctor()->create($doctorData);
        });

        return (new DoctorCardResource($doctor->load(['user', 'city'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Doctor $doctor)
    {
        return new DoctorCardResource($doctor->load(['user', 'city']));
    }

    public function update(UpdateDoctorRequest $request, Doctor $doctor)
    {
        $doctor->update(Arr::only($request->validated(), [
            'city_id',
            'specialty',
            'license_number',
            'degrees',
            'bio',
            'available_slots',
            'status',
        ]));

        if ($request->filled('name') || $request->filled('phone') || $request->filled('status')) {
            $doctor->user->update(array_filter([
                'name' => $request->name,
                'phone' => $request->phone,
                'status' => $request->status,
            ], fn ($value) => $value !== null));
        }

        return new DoctorCardResource($doctor->load(['user', 'city']));
    }

    public function destroy(Doctor $doctor)
    {
        DB::transaction(function () use ($doctor) {
            $doctor->delete();
            $doctor->user()->delete();
        });

        return response()->noContent();
    }
}
