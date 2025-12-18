<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToJobWorkChallansTable extends Migration
{
    public function up()
    {
        Schema::table('job_work_challans', function (Blueprint $table) {
            $table->string('po_revision_and_date')->nullable()->after('pdf_files');
            $table->string('reason_of_revision')->nullable()->after('po_revision_and_date');
            $table->string('quotation_ref_no')->nullable()->after('reason_of_revision');
            $table->text('remarks')->nullable()->after('quotation_ref_no');
            $table->date('pr_date')->nullable()->after('remarks');
            $table->string('prno')->after('pr_date');
        });
    }

    public function down()
    {
        Schema::table('job_work_challans', function (Blueprint $table) {
            $table->dropColumn([
                'po_revision_and_date',
                'reason_of_revision',
                'quotation_ref_no',
                'remarks',
                'pr_date',
                'prno',
            ]);
        });
    }
}
