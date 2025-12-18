<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('valve_type');
            $table->unsignedBigInteger('product_code');
            $table->string('actuation');
            $table->string('pressure_rating');
            $table->string('valve_size');
            $table->enum('valve_size_rate', ['inch', 'centimeter'])->default('inch');
            $table->string('media');
            $table->string('flow');
            $table->string('sku_code');
            $table->string('mrp');
            $table->string('media_temperature');
            $table->enum('media_temperature_rate', ['FAHRENHEIT', 'CELSIUS'])->default('CELSIUS');
            $table->string('body_material');
            $table->string('hsn_code');
            $table->string('primary_material_of_construction');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
