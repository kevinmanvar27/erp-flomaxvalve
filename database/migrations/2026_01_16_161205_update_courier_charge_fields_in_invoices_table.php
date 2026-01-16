<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First add new columns
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('courier_charge_before_gst', 10, 2)->default(0)->after('pfcouriercharge');
            $table->decimal('courier_charge_after_gst', 10, 2)->default(0)->after('courier_charge_before_gst');
        });
        
        // Migrate existing data: if courier_charge_enabled was true, move to before_gst, else to after_gst
        DB::statement("
            UPDATE invoices 
            SET courier_charge_before_gst = CASE WHEN courier_charge_enabled = 1 THEN COALESCE(courier_charge, 0) ELSE 0 END,
                courier_charge_after_gst = CASE WHEN courier_charge_enabled = 0 OR courier_charge_enabled IS NULL THEN COALESCE(courier_charge, 0) ELSE 0 END
        ");
        
        // Now drop old columns
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['courier_charge', 'courier_charge_enabled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back old columns
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('courier_charge', 10, 2)->default(0)->after('pfcouriercharge');
            $table->boolean('courier_charge_enabled')->default(false)->after('courier_charge');
        });
        
        // Migrate data back - combine both fields, set enabled if before_gst had value
        DB::statement("
            UPDATE invoices 
            SET courier_charge = COALESCE(courier_charge_before_gst, 0) + COALESCE(courier_charge_after_gst, 0),
                courier_charge_enabled = CASE WHEN courier_charge_before_gst > 0 THEN 1 ELSE 0 END
        ");
        
        // Drop new columns
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['courier_charge_before_gst', 'courier_charge_after_gst']);
        });
    }
};
