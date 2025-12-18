<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantityToProductSparePartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_spare_part', function (Blueprint $table) {
            // Add quantity column with default value 0
            $table->integer('quantity')->default(0)->after('spare_part_id');
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
            // Remove the quantity column if the migration is rolled back
            $table->dropColumn('quantity');
        });
    }
}
