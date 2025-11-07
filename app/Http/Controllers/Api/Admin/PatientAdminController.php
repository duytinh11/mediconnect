<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePatientRequest;
use App\Http\Requests\Admin\UpdatePatientRequest;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PatientAdminController extends Controller
{
    public function index()
    {
        return Patient::with('user')->latest()->paginate();
    }

    public function store(StorePatientRequest $request)
    {
        $patient = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => strtolower($request->email),
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'patient',
                'status' => $request->status ?? 'active',
            ]);

            $patientData = Arr::only($request->validated(), [
                'address',
                'gender',
                'dob',
            ]);

            return $user->patient()->create($patientData);
        });

        return response()->json($patient->load('user'), 201);
    }

    public function show(Patient $patient)
    {
        return $patient->load('user');
    }

    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        $patient->update(Arr::only($request->validated(), [
            'address',
            'gender',
            'dob',
        ]));

        if ($request->filled('name') || $request->filled('phone') || $request->filled('status')) {
            $patient->user->update(array_filter([
                'name' => $request->name,
                'phone' => $request->phone,
                'status' => $request->status,
            ], fn ($value) => $value !== null));
        }

        return $patient->load('user');
    }

    public function destroy(Patient $patient)
    {
        DB::transaction(function () use ($patient) {
            $patient->delete();
            $patient->user()->delete();
        });

        return response()->noContent();
    }
}
