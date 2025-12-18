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
        Schema::table('new_purchase_order_item', function (Blueprint $table) {
            // Add the spare_part_id column with foreign key constraint
            $table->foreignId('spare_part_id')->constrained('spare_parts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('new_purchase_order_item', function (Blueprint $table) {
            // Re-add the product_id column and foreign key constraint
            $table->dropColumn('spare_part_id');
        });
    }
};
