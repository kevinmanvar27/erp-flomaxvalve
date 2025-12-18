<?php


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'permissions' => json_encode(['read', 'write', 'update']),
        ]);

        User::create([
            'name' => 'Sales User',
            'email' => 'sales@example.com',
            'password' => Hash::make('password'),
            'role' => 'sales',
            'permissions' => json_encode(['read']),
        ]);
    }
}
