<?php

namespace Database\Seeders;

use App\Models\Medicine;
use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicines = [
            ['name' => 'Paracetamol', 'type' => 'tablet', 'dosage_options' => '500mg, 650mg, 1000mg'],
            ['name' => 'Amoxicillin', 'type' => 'capsule', 'dosage_options' => '250mg, 500mg'],
            ['name' => 'Ibuprofen', 'type' => 'tablet', 'dosage_options' => '200mg, 400mg, 600mg'],
            ['name' => 'Cough Syrup', 'type' => 'syrup', 'dosage_options' => '5ml, 10ml'],
            ['name' => 'Ceftriaxone', 'type' => 'injection', 'dosage_options' => '1g, 2g'],
            ['name' => 'Omeprazole', 'type' => 'capsule', 'dosage_options' => '20mg, 40mg'],
            ['name' => 'Cetirizine', 'type' => 'tablet', 'dosage_options' => '10mg'],
        ];

        foreach ($medicines as $medicine) {
            Medicine::create($medicine);
        }
    }
}
