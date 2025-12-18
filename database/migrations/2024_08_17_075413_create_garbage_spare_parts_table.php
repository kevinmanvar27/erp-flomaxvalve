<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGarbageSparePartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('garbage_spare_parts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('garbage_id');
            $table->unsignedBigInteger('spare_part_id');
            $table->string('type');
            $table->string('size');
            $table->decimal('weight', 8, 2);
            $table->integer('quantity');
            $table->timestamps();

            $table->foreign('garbage_id')->references('id')->on('garbages')->onDelete('cascade');
            $table->foreign('spare_part_id')->references('id')->on('spare_parts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('garbage_spare_parts');
    }
}
