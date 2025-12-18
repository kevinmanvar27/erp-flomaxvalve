<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('stake_holders', function (Blueprint $table) {
            $table->string('last_name')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('stake_holders', function (Blueprint $table) {
            $table->string('last_name')->nullable(false)->change();
        });
    }

};
