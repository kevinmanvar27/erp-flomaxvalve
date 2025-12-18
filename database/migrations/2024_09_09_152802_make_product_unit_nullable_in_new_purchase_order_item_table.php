<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_purchase_order_item', function (Blueprint $table) {
            $table->string('product_unit')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_purchase_order_item', function (Blueprint $table) {
            $table->string('product_unit')->nullable(false)->change(); // Revert back to non-nullable if needed
        });
    }
};
