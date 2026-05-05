<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContributorImageSubmissionUpdates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contributor_image_submission_updates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('file');
            $table->integer('contributor_id');
            $table->enum('status', ['pending', 'processing', 'done', 'error'])->default('pending');
            $table->longText('updated_files')->nullable();
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
        Schema::dropIfExists('contributor_image_submission_updates');
    }
}
