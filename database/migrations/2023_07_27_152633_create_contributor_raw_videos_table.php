<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContributorRawVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contributor_raw_videos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('contributor_video_id')->nullable();
            $table->string('extension');
            $table->enum('status',['pending','active'])->default('pending');
            $table->tinyInteger('contributor_stage')->default(0);
            $table->string('review_notes')->default('');
            $table->string('hash');
            $table->string('original_name');
            $table->tinyInteger('has_noise');
            $table->string('original',500);
            $table->tinyInteger('is_uploaded_original');
            $table->string('duration');
            $table->string('preview',500);
            $table->tinyInteger('is_uploaded_preview');
            $table->timestamps(); 
            $table->softDeletes();

            // $table->foreign('contributor_video_id')->references('id')->on('contributor_videos')->onDelete('NO ACTION');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contributor_raw_videos');
    }
}
