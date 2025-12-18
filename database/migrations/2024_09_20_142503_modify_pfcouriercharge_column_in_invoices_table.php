<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyPfcourierchargeColumnInInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Modify 'pfcouriercharge' column to store integers (removing decimal)
            $table->integer('pfcouriercharge')->change()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Revert back to decimal in case you want to roll back
            $table->decimal('pfcouriercharge', 15, 2)->change();
        });
    }
}

