<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPoNoToJobWorkChallansTable extends Migration
{
    public function up()
    {
        Schema::table('job_work_challans', function (Blueprint $table) {
            $table->string('po_no')->nullable()->after('prno'); // Add po_no column
        });
    }

    public function down()
    {
        Schema::table('job_work_challans', function (Blueprint $table) {
            $table->dropColumn('po_no'); // Remove the po_no column if rolling back
        });
    }
}
