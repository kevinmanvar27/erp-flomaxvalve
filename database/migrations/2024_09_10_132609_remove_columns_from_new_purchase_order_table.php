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
            // Dropping the specified columns
            $table->dropColumn([
                'sub_total',
                'discount',
                'balance',
                'discount_type',
                'cgst',
                'sgst'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('new_purchase_order', function (Blueprint $table) {
            // Re-adding the columns in case of rollback
            $table->decimal('sub_total', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->decimal('balance', 10, 2)->nullable();
            $table->string('discount_type')->nullable();
            $table->decimal('cgst', 5, 2)->nullable();
            $table->decimal('sgst', 5, 2)->nullable();
        });
    }
};
