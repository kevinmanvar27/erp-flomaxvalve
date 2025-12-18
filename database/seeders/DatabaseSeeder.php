<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Creating a test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Call the other seeders
        $this->call([
            SparePartsTableSeeder::class,
            StakeHoldersSeeder::class,
            ProductsTableSeeder::class,
            ProductSparePartTableSeeder::class,
            InventoriesSeeder::class,
            InventoryItemsSeeder::class,
            GarbageSeeder::class,
            GarbageSparePartsSeeder::class,
        ]);
    }
}
