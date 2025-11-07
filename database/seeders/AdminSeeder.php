<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $city = City::firstOrCreate(['name' => 'Ho Chi Minh City']);

        $admin = User::firstOrCreate(['email' => 'admin@mediconnect.com'], [
            'name' => 'System Admin',
            'phone' => '0123456789',
            'password' => Hash::make('Admin@123'),
            'role' => 'admin',
        ]);

        $doctorUser = User::firstOrCreate(['email' => 'doctor@mediconnect.com'], [
            'name' => 'Dr. Alice Nguyen',
            'phone' => '0909000000',
            'password' => Hash::make('Doctor@123'),
            'role' => 'doctor',
        ]);

        Doctor::firstOrCreate(['user_id' => $doctorUser->id], [
            'city_id' => $city->id,
            'specialty' => 'Cardiology',
            'license_number' => 'LIC-1001',
            'degrees' => 'MD',
        ]);

        $patientUser = User::firstOrCreate(['email' => 'patient@mediconnect.com'], [
            'name' => 'John Patient',
            'phone' => '0988000000',
            'password' => Hash::make('Patient@123'),
            'role' => 'patient',
        ]);

        Patient::firstOrCreate(['user_id' => $patientUser->id], [
            'address' => '123 Nguyen Van Linh',
        ]);
    }
}
