<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'scheduled_at' => $this->scheduled_at->toAtomString(),
            'status' => $this->status,
            'reason' => $this->reason,
            'doctor' => [
                'id' => $this->doctor->id,
                'name' => $this->doctor->user->name,
                'specialty' => $this->doctor->specialty,
            ],
            'patient' => [
                'id' => $this->patient->id,
                'name' => $this->patient->user->name,
            ],
        ];
    }
}
