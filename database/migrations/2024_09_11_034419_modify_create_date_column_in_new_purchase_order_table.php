<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyCreateDateColumnInNewPurchaseOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_purchase_order', function (Blueprint $table) {
            // Modifying the 'create_date' column to allow NULL values
            $table->date('create_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_purchase_order', function (Blueprint $table) {
            // Reverting the 'create_date' column to NOT NULL
            $table->date('create_date')->nullable(false)->change();
        });
    }
}
