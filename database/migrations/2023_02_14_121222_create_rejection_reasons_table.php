<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRejectionReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rejection_reasons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('type',['permanently','temporarily']);
            $table->enum('status',['active','inactive'])->default('active');
            $table->string('title')->nullable();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rejection_reasons');
    }
}
