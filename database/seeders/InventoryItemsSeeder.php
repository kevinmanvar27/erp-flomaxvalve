<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class InventoryItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $sparePartIds = DB::table('spare_parts')->pluck('id')->toArray();
        $inventoryIds = DB::table('inventories')->pluck('id')->toArray();

        foreach (range(1, 20) as $index) {
            DB::table('inventory_items')->insert([
                'amount' => $faker->numberBetween(100, 1000),
                'quantity' => $faker->numberBetween(1, 100),
                'sku_code' => strtoupper($faker->unique()->bothify('SKU-#####')),
                'spare_part_id' => $faker->randomElement($sparePartIds),
                'inventory_id' => $faker->randomElement($inventoryIds),
            ]);
        }
    }
}
