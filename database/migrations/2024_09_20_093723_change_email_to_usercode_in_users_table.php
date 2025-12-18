<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeEmailToUsercodeInUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Rename the column
            $table->renameColumn('email', 'usercode');

            // Change the type to string (or another type if needed) and add a check constraint for digits
            $table->string('usercode')->unique()->change(); // Ensure uniqueness
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert the column name back to email
            $table->renameColumn('usercode', 'email');
        });
    }
}
