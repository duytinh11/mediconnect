<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Notifications\AppointmentStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        return $this->wrap(function () use ($request) {
            $user = $request->user();

            $appointments = Appointment::with(['doctor.user','patient.user'])
                ->when($user->role === 'doctor', fn($q) => $q->where('doctor_id', $user->doctor->id))
                ->when($user->role === 'patient', fn($q) => $q->where('patient_id', $user->patient->id))
                ->latest('scheduled_at')
                ->paginate();

            return AppointmentResource::collection($appointments);
        });
    }

    public function store(StoreAppointmentRequest $request)
    {
        return $this->wrap(function () use ($request) {
            $patientId = $request->user()->patient->id;
            $data = $request->validated();

            $exists = Appointment::where('doctor_id', $data['doctor_id'])
                ->where('scheduled_at', $data['scheduled_at'])
                ->whereIn('status', ['pending','confirmed'])
                ->exists();

            if ($exists) {
                return response()->json(['message' => 'Slot already booked'], 409);
            }

            $appointment = Appointment::create([
                'doctor_id' => $data['doctor_id'],
                'patient_id' => $patientId,
                'scheduled_at' => $data['scheduled_at'],
                'status' => 'pending',
                'reason' => $data['reason'] ?? null,
            ]);

            // example: notify doctor here (may throw) -- wrapped so will be logged
            return new AppointmentResource($appointment->load(['doctor.user','patient.user']));
        });
    }

    public function show(Appointment $appointment)
    {
        return $this->wrap(function () use ($appointment) {
            $appointment->load(['doctor.user','patient.user']);
            $this->authorizeView($appointment);
            return new AppointmentResource($appointment);
        });
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        $this->authorizeModify($appointment);
        $data = $request->validated();
        $appointment->load(['doctor.user', 'patient.user']);

        if (isset($data['scheduled_at'])) {
            $exists = Appointment::where('doctor_id', $appointment->doctor_id)
                ->where('scheduled_at', $data['scheduled_at'])
                ->where('id', '!=', $appointment->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            if ($exists) {
                return response()->json(['message' => 'Slot already booked'], 409);
            }
        }

        $appointment->fill($data)->save();

        $type = isset($data['scheduled_at']) ? 'reschedule' : 'status';
        if (($data['status'] ?? null) === 'canceled') {
            $type = 'cancel';
        }

        $this->notifyParticipants($appointment, $type);

        return new AppointmentResource($appointment);
    }

    public function destroy(Appointment $appointment)
    {
        $this->authorizeModify($appointment);
        $appointment->update(['status' => 'canceled']);
        $appointment->load(['doctor.user', 'patient.user']);

        $this->notifyParticipants($appointment, 'cancel');

        return response()->noContent();
    }

    protected function authorizeView(Appointment $appointment): void
    {
        $user = request()->user();
        $allowed = $user->role === 'admin'
            || ($user->role === 'doctor' && $appointment->doctor_id === optional($user->doctor)->id)
            || ($user->role === 'patient' && $appointment->patient_id === optional($user->patient)->id);

        abort_unless($allowed, 403);
    }

    protected function authorizeModify(Appointment $appointment): void
    {
        $user = request()->user();
        $allowed = $user->role === 'admin'
            || ($user->role === 'doctor' && $appointment->doctor_id === optional($user->doctor)->id)
            || ($user->role === 'patient' && $appointment->patient_id === optional($user->patient)->id);

        abort_unless($allowed, 403);
    }

    protected function notifyParticipants(Appointment $appointment, string $type): void
    {
        $doctorUser = $appointment->doctor->user;
        $patientUser = $appointment->patient->user;
        Notification::send([$doctorUser, $patientUser], new AppointmentStatusNotification($appointment, $type));
    }
}
