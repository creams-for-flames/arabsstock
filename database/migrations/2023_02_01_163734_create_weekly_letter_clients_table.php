<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeeklyLetterClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weekly_letter_clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('sent')->default(false);
            $table->enum('target',['all','custom']);
            $table->text('custom_target')->nullable();
            $table->integer('target_count')->nullable(); 
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
        Schema::dropIfExists('weekly_letter_clients');
    }
}
