<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeProductFieldsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('name', 255)->nullable()->change();
            $table->string('valve_type', 255)->nullable()->change();
            $table->bigInteger('product_code')->nullable()->change();
            $table->string('actuation', 255)->nullable()->change();
            $table->string('pressure_rating', 255)->nullable()->change();
            $table->string('valve_size', 255)->nullable()->change();
            $table->enum('valve_size_rate', ['inch', 'centimeter'])->nullable()->change();
            $table->string('media', 255)->nullable()->change();
            $table->string('flow', 255)->nullable()->change();
            $table->string('sku_code', 255)->nullable()->change();
            $table->string('mrp', 255)->nullable()->change();
            $table->string('media_temperature', 255)->nullable()->change();
            $table->enum('media_temperature_rate', ['FAHRENHEIT', 'CELSIUS'])->nullable()->change();
            $table->string('body_material', 255)->nullable()->change();
            $table->string('hsn_code', 255)->nullable()->change();
            $table->string('primary_material_of_construction', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('name', 255)->nullable(false)->change();
            $table->string('valve_type', 255)->nullable(false)->change();
            $table->bigInteger('product_code')->nullable(false)->change();
            $table->string('actuation', 255)->nullable(false)->change();
            $table->string('pressure_rating', 255)->nullable(false)->change();
            $table->string('valve_size', 255)->nullable(false)->change();
            $table->enum('valve_size_rate', ['inch', 'centimeter'])->nullable(false)->change();
            $table->string('media', 255)->nullable(false)->change();
            $table->string('flow', 255)->nullable(false)->change();
            $table->string('sku_code', 255)->nullable(false)->change();
            $table->string('mrp', 255)->nullable(false)->change();
            $table->string('media_temperature', 255)->nullable(false)->change();
            $table->enum('media_temperature_rate', ['FAHRENHEIT', 'CELSIUS'])->nullable(false)->change();
            $table->string('body_material', 255)->nullable(false)->change();
            $table->string('hsn_code', 255)->nullable(false)->change();
            $table->string('primary_material_of_construction', 255)->nullable(false)->change();
        });
    }
}