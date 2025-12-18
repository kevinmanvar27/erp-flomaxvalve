<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInternalRejectionsTable extends Migration
{
    public function up()
    {
        Schema::create('internal_rejections', function (Blueprint $table) {
            $table->id();
            
            // User code column with foreign key to users table
            $table->string('user_code');
            $table->foreign('user_code')->references('usercode')->on('users')->onDelete('cascade');
            
            // Parts and Quantity
            $table->string('parts');
            $table->integer('qty');
            
            // Reason column (nullable)
            $table->text('reason')->nullable();
            
            // Timestamps
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('internal_rejections');
    }
}
