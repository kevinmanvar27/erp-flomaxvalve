<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        $products = [];

        for ($i = 1; $i <= 30; $i++) {
            $products[] = [
                'name' => 'Product ' . $i,
                'valve_type' => 'Type ' . $i,
                'product_code' => rand(1000, 9999),
                'actuation' => 'Manual',
                'pressure_rating' => 'High',
                'valve_size' => rand(1, 10) . ' inch',
                'valve_size_rate' => 'inch',
                'media' => 'Water',
                'flow' => rand(10, 100) . ' L/min',
                'sku_code' => 'SKU' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'mrp' => rand(1000, 9999) . ' USD',
                'media_temperature' => rand(20, 100) . ' °C',
                'media_temperature_rate' => 'CELSIUS',
                'body_material' => 'Steel',
                'hsn_code' => 'HSN' . rand(1000, 9999),
                'primary_material_of_construction' => 'Carbon Steel',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('products')->insert($products);
    }
}
