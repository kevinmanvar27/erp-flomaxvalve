<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMinimumQtyToSparepartsTable extends Migration
{
    public function up()
    {
        Schema::table('spare_parts', function (Blueprint $table) {
            $table->integer('minimum_qty')->nullable()->after('qty'); // Adjust the position as needed
        });
    }

    public function down()
    {
        Schema::table('spare_parts', function (Blueprint $table) {
            $table->dropColumn('minimum_qty');
        });
    }
}
