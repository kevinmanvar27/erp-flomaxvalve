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
        Schema::create('new_purchase_order_item', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->decimal('amount', 15, 2);
            $table->string('product_unit');
            $table->string('remark');
            $table->foreignId('new_purchase_order_id')->constrained('new_purchase_order_item')->onDelete('cascade');
            $table->foreignId('spare_part_id')->constrained('spare_parts')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_purchase_order_item');
    }
};
