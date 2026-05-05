<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->integer('id', true);
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->string('nationality')->nullable();
            $table->string('nationality_one')->nullable();
            $table->string('skill')->nullable();
            $table->string('city')->nullable();
            $table->string('sex')->nullable();
            $table->string('social')->nullable();
            $table->string('length')->nullable();
            $table->string('weight')->nullable();
            $table->string('work_field')->nullable();
            $table->string('birth_date')->nullable();
            $table->string('skills')->nullable();
            $table->integer('status')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('date')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
