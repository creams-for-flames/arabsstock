<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeeklyLetterClientFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weekly_letter_client_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('weekly_letter_client_id');
            $table->unsignedBigInteger('fileable_id');
            $table->string('fileable_type');
            $table->enum('file_type',['category','file'])->default('file');
            $table->string('file_title');
            $table->string('file_path');
            $table->string('file_url');
            $table->boolean('image_generated')->default(false);
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
        Schema::dropIfExists('weekly_letter_client_files');
    }
}
