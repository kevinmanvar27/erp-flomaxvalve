<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->string('invoice_number');
            $table->date('create_date');
            $table->foreignId('supplier_id')->constrained('stake_holders')->onDelete('cascade');
            $table->foreignId('spare_part_id')->constrained('spare_parts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn('invoice_number');
            $table->dropColumn('create_date');
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['spare_part_id']);
            $table->dropColumn('supplier_id');
            $table->dropColumn('spare_part_id');
        });
    }
}
