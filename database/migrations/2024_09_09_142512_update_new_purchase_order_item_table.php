<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('new_purchase_order', function (Blueprint $table) {
            // Remove the columns 'item_tax' and 'invoice_tax'
            $table->dropColumn(['item_tax', 'invoice_tax']);

            // Add the new columns 'discount_type', 'cgst', and 'sgst'
            $table->string('discount_type')->nullable();
            $table->decimal('cgst', 8, 2)->nullable();
            $table->decimal('sgst', 8, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('new_purchase_order', function (Blueprint $table) {
            // Add back the removed columns 'item_tax' and 'invoice_tax'
            $table->decimal('item_tax', 8, 2)->nullable();
            $table->decimal('invoice_tax', 8, 2)->nullable();

            // Remove the newly added columns
            $table->dropColumn(['discount_type', 'cgst', 'sgst']);
        });
    }
};
