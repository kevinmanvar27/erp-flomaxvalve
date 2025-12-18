<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchaseOrderColumnsToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Adding new columns
            $table->string('purchase_order_gstin')->nullable()->after('company_gstin');
            $table->string('purchase_order_mobile_number')->nullable()->after('purchase_order_gstin');
            $table->string('purchase_order_email')->nullable()->after('purchase_order_mobile_number');
            $table->string('purchase_order_address')->nullable()->after('purchase_order_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Dropping columns if the migration is rolled back
            $table->dropColumn([
                'purchase_order_gstin',
                'purchase_order_mobile_number',
                'purchase_order_email',
                'purchase_order_address'
            ]);
        });
    }
}
