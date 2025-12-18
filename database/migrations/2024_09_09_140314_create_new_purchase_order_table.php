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
        Schema::create('new_purchase_order', function (Blueprint $table) {
            $table->id();
            $table->string('address');
            $table->date('create_date');
            $table->date('due_date');
            $table->string('invoice');
            $table->string('status');
            $table->decimal('sub_total', 15, 2);
            $table->decimal('item_tax', 15, 2);
            $table->decimal('invoice_tax', 15, 2);
            $table->decimal('discount', 15, 2);
            $table->decimal('balance', 15, 2);
            $table->foreignId('customer_id')->constrained('stake_holders')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_purchase_order');
    }
};
