<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyDueDateColumnInNewPurchaseOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_purchase_order', function (Blueprint $table) {
            // Modify the due_date column to allow NULL
            $table->date('due_date')->nullable()->change();
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
            // Revert the due_date column to NOT NULL if needed
            $table->date('due_date')->nullable(false)->change();
        });
    }
}
