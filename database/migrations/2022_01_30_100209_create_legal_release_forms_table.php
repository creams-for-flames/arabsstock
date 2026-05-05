<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLegalReleaseFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('legal_release_forms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('file');
            $table->string('description');
            $table->string('local');
            $table->enum('status',['active','pending'])->default('active');
            $table->enum('type',['property','minor','adult']);
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
        Schema::dropIfExists('legal_release_forms');
    }
}
