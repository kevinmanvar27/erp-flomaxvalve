<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogoAndAuthorizedSignatoryToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Add new columns
            $table->string('prepared_by')->nullable()->after('authorized_signatory');
            $table->string('approved_by')->nullable()->after('prepared_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Remove columns if rolling back
            $table->dropColumn([
                'prepared_by',
                'approved_by'
            ]);
        });
    }
}
