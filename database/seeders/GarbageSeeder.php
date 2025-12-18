<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Garbage;

class GarbageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed example garbage data
        Garbage::create([
            'product_id' => 1,
            'type' => 'client',
            'quantity' => 10,
        ]);

        Garbage::create([
            'product_id' => 2,
            'type' => 'own',
            'quantity' => 15,
        ]);
    }
}
