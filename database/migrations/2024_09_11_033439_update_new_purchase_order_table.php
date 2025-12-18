<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNewPurchaseOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_purchase_order', function (Blueprint $table) {
            // Renaming the column
            $table->renameColumn('lrno', 'prno');
            
            // Adding new columns with underscores
            $table->string('po_revision_and_date')->nullable();
            $table->string('reason_of_revision')->nullable();
            $table->string('quotation_ref_no')->nullable();
            $table->text('remarks')->nullable();
            $table->date('pr_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_purchase_order', function (Blueprint $table) {
            // Reverting the changes
            $table->renameColumn('prno', 'lrno');
            
            // Dropping the new columns
            $table->dropColumn(['po_revision_and_date', 'reason_of_revision', 'quotation_ref_no', 'remarks', 'pr_date']);
        });
    }
}
