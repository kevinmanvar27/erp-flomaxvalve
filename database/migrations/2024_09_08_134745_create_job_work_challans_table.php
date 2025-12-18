<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobWorkChallansTable extends Migration
{
    public function up()
    {
        Schema::create('job_work_challans', function (Blueprint $table) {
            $table->id();
            $table->string('job_work_name');
            $table->json('pdf_files'); // to store multiple file paths
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_work_challans');
    }
}
