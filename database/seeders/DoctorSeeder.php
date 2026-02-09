<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Doctor::create([
            'name' => 'John Doe',
            'email' => 'doctor@example.com',
            'password' => Hash::make('password'),
            'specialization' => 'General Physician',
        ]);

        Doctor::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'specialization' => 'Pediatrician',
        ]);
    }
}
