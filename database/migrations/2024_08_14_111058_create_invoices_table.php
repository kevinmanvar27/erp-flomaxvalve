<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
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
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
