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
        Schema::table('stake_holders', function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('state_code')->nullable();
            $table->string('GSTIN')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_no')->nullable();
            $table->string('ifsc_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stake_holders', function (Blueprint $table) {
            $table->dropColumn(['address', 'state', 'city', 'state_code', 'GSTIN', 'bank_name', 'bank_account_no', 'ifsc_code']);
        });
    }
};
