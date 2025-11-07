<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function update(UpdateProfileRequest $request)
    {
        return $this->wrap(function () use ($request) {
            $user = $request->user();
            $data = $request->validated();

            if ($request->hasFile('avatar')) {
                if ($user->avatar_path) {
                    Storage::disk('public')->delete($user->avatar_path);
                }

                $data['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
            }

            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user->fill(Arr::only($data, ['name','email','phone','password','avatar_path']))->save();

            if ($user->patient) {
                $user->patient->update(Arr::only($data, ['address','gender','dob']));
            }

            if ($user->doctor) {
                $user->doctor->update(Arr::only($data, [
                    'specialty',
                    'degrees',
                    'bio',
                    'license_number',
                    'available_slots',
                    'city_id',
                ]));
            }

            return response()->json([
                'user' => $user->load(['patient','doctor.city']),
            ]);
        });
    }
}
