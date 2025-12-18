<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GarbageSparePart;

class GarbageSparePartsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed example garbage spare part data
        GarbageSparePart::create([
            'garbage_id' => 1,
            'spare_part_id' => 1,
            'type' => 'client',
            'size' => 'large',
            'weight' => 10.5,
            'quantity' => 5,
        ]);

        GarbageSparePart::create([
            'garbage_id' => 2,
            'spare_part_id' => 2,
            'type' => 'own',
            'size' => 'medium',
            'weight' => 7.2,
            'quantity' => 8,
        ]);
    }
}
