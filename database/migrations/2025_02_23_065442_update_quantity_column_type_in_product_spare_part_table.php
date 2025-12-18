<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateQuantityColumnTypeInProductSparePartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_spare_part', function (Blueprint $table) {
            $table->decimal('quantity', 10, 4)->change(); // 10 total digits with 4 decimal places
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_spare_part', function (Blueprint $table) {
            $table->bigInteger('quantity')->change();
        });
    }
}