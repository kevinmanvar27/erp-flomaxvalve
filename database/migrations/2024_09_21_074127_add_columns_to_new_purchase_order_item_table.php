<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToNewPurchaseOrderItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_purchase_order_item', function (Blueprint $table) {
            $table->string('material_specification')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('rate_kgs', 8, 2)->nullable();
            $table->decimal('per_pc_weight', 8, 2)->nullable();
            $table->decimal('total_weight', 8, 2)->nullable();
            $table->date('delivery_date')->nullable();
            // Modify the existing column to allow null
            $table->decimal('amount', 8, 2)->nullable()->change();
            $table->decimal('price', 8, 2)->nullable()->change();
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
            $table->dropColumn([
                'material_specification',
                'unit',
                'rate_kgs',
                'per_pc_weight',
                'total_weight',
                'delivery_date'
            ]);
            // Revert the existing column back to not null if necessary
            $table->decimal('amount', 8, 2)->nullable(false)->change();
            $table->decimal('price', 8, 2)->nullable(false)->change();
        });
    }
}
