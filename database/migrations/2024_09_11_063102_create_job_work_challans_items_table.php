<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobWorkChallansItemsTable extends Migration
{
    public function up()
    {
        Schema::create('job_work_challan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_work_challans_id')->constrained('job_work_challans')->onDelete('cascade');
            $table->foreignId('spare_part_id')->constrained('spare_parts')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('wt_pc', 10, 2); // Allows decimal values for amounts
            $table->text('material_specification')->nullable();
            $table->text('remark')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_work_challan_items');
    }
}
