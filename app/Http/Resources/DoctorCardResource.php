<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'status' => $this->user->status,
            'specialty' => $this->specialty,
            'degrees' => $this->degrees,
            'license_number' => $this->license_number,
            'bio' => $this->bio,
            'city' => $this->city?->name,
            'city_id' => $this->city_id,
            'available_slots' => $this->available_slots ?? [],
            'created_at' => $this->created_at,
        ];
    }
}
