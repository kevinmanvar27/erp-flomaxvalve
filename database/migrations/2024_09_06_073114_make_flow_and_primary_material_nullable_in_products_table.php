<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeFlowAndPrimaryMaterialNullableInProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('flow')->nullable()->change();
            $table->string('primary_material_of_construction')->nullable()->change();
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
            $table->string('flow')->nullable(false)->change(); // Revert back to non-nullable if needed
            $table->string('primary_material_of_construction')->nullable(false)->change();
        });
    }
}
