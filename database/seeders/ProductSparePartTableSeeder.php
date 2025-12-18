<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSparePartTableSeeder extends Seeder
{
    public function run()
    {
        $productSpareParts = [];

        for ($i = 1; $i <= 30; $i++) {
            $productSpareParts[] = [
                'product_id' => rand(1, 30),
                'spare_part_id' => rand(1, 30),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('product_spare_part')->insert($productSpareParts);
    }
}
