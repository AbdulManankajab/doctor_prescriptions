<?php

namespace Database\Seeders;

use App\Models\DefaultPrescriptionDetail;
use Illuminate\Database\Seeder;

class DefaultPrescriptionDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $details = [
            ['detail_text' => 'Take rest and drink plenty of water.'],
            ['detail_text' => 'Avoid oily and spicy food for 3 days.'],
            ['detail_text' => 'Follow up after 7 days if symptoms persist.'],
            ['detail_text' => 'Take medicines regularly at the same time.'],
        ];

        foreach ($details as $detail) {
            DefaultPrescriptionDetail::create($detail);
        }
    }
}
