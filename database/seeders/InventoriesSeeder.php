<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class InventoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $supplierIds = DB::table('stake_holders')->where('user_type', 'supplier')->pluck('id')->toArray();
        $sparePartIds = DB::table('spare_parts')->pluck('id')->toArray();

        foreach (range(1, 20) as $index) {
            DB::table('inventories')->insert([
                'invoice_number' => strtoupper($faker->unique()->bothify('INV-#####')),
                'create_date' => $faker->date(),
                'supplier_id' => $faker->randomElement($supplierIds),
            ]);
        }
    }
}
