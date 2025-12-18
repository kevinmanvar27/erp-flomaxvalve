<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SparePartsTableSeeder extends Seeder
{
    public function run()
    {
        // Example data with random qty
        $spareParts = [
            ['name' => 'Brake Pad', 'type' => 'Braking System', 'size' => 'Medium', 'weight' => 1.5, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Air Filter', 'type' => 'Engine', 'size' => 'Small', 'weight' => 0.3, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Oil Filter', 'type' => 'Engine', 'size' => 'Small', 'weight' => 0.4, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Spark Plug', 'type' => 'Engine', 'size' => 'Standard', 'weight' => 0.2, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Timing Belt', 'type' => 'Engine', 'size' => 'Large', 'weight' => 0.8, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Clutch Kit', 'type' => 'Transmission', 'size' => 'Medium', 'weight' => 3.0, 'unit' => 'Kit', 'qty' => rand(1, 100)],
            ['name' => 'Alternator', 'type' => 'Electrical', 'size' => 'Standard', 'weight' => 4.5, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Battery', 'type' => 'Electrical', 'size' => 'Standard', 'weight' => 5.0, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Radiator', 'type' => 'Cooling System', 'size' => 'Large', 'weight' => 7.0, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Water Pump', 'type' => 'Cooling System', 'size' => 'Medium', 'weight' => 1.2, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Fuel Pump', 'type' => 'Fuel System', 'size' => 'Small', 'weight' => 0.8, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Drive Shaft', 'type' => 'Drivetrain', 'size' => 'Large', 'weight' => 6.0, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'CV Joint', 'type' => 'Drivetrain', 'size' => 'Medium', 'weight' => 2.0, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Shock Absorber', 'type' => 'Suspension', 'size' => 'Large', 'weight' => 3.5, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Strut Assembly', 'type' => 'Suspension', 'size' => 'Large', 'weight' => 4.0, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Control Arm', 'type' => 'Suspension', 'size' => 'Medium', 'weight' => 2.5, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Tie Rod', 'type' => 'Steering', 'size' => 'Small', 'weight' => 1.0, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Steering Rack', 'type' => 'Steering', 'size' => 'Standard', 'weight' => 5.0, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Wheel Bearing', 'type' => 'Wheel', 'size' => 'Standard', 'weight' => 1.2, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Hub Assembly', 'type' => 'Wheel', 'size' => 'Large', 'weight' => 3.0, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Brake Rotor', 'type' => 'Braking System', 'size' => 'Standard', 'weight' => 2.5, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Brake Caliper', 'type' => 'Braking System', 'size' => 'Standard', 'weight' => 2.0, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Headlight', 'type' => 'Lighting', 'size' => 'Standard', 'weight' => 0.7, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Tail Light', 'type' => 'Lighting', 'size' => 'Standard', 'weight' => 0.6, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Turn Signal', 'type' => 'Lighting', 'size' => 'Small', 'weight' => 0.4, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Wiper Blade', 'type' => 'Wiper System', 'size' => 'Standard', 'weight' => 0.5, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Windshield Washer Pump', 'type' => 'Wiper System', 'size' => 'Small', 'weight' => 0.3, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Air Conditioning Compressor', 'type' => 'AC System', 'size' => 'Standard', 'weight' => 4.0, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Condenser', 'type' => 'AC System', 'size' => 'Standard', 'weight' => 2.5, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Expansion Valve', 'type' => 'AC System', 'size' => 'Small', 'weight' => 0.6, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Evaporator Core', 'type' => 'AC System', 'size' => 'Standard', 'weight' => 3.0, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Serpentine Belt', 'type' => 'Engine', 'size' => 'Medium', 'weight' => 0.7, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Timing Chain', 'type' => 'Engine', 'size' => 'Medium', 'weight' => 1.2, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Valve Cover Gasket', 'type' => 'Engine', 'size' => 'Standard', 'weight' => 0.5, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Cylinder Head', 'type' => 'Engine', 'size' => 'Large', 'weight' => 8.0, 'unit' => 'Piece', 'qty' => rand(1, 100)],
            ['name' => 'Piston Ring Set', 'type' => 'Engine', 'size' => 'Standard', 'weight' => 0.8, 'unit' => 'Set', 'qty' => rand(1, 100)],
        ];

        // Insert data into the spare_parts table
        DB::table('spare_parts')->insert($spareParts);
    }
}
