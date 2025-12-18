<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateForeignKeyOnNewPurchaseOrderItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_purchase_order_item', function (Blueprint $table) {
            // Drop the existing foreign key
            $table->dropForeign(['new_purchase_order_id']);

            // Re-add the foreign key with cascade on delete
            $table->foreign('new_purchase_order_id')
                  ->references('id')
                  ->on('new_purchase_order')
                  ->onDelete('cascade');
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
            // Drop the foreign key added in the up() method
            $table->dropForeign(['new_purchase_order_id']);

            // Optionally, if you want to revert to the old foreign key definition, you can add it back here
            // Example (assuming the original constraint didn't have cascade on delete):
            $table->foreign('new_purchase_order_id')
                  ->references('id')
                  ->on('new_purchase_order');
        });
    }
}
