<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContributorVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contributor_videos', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->string('thumbnail');
            $table->integer('user_id');
            $table->integer('thumbnail_width');
            $table->integer('thumbnail_height');
            $table->string('title_ar');
            $table->string('title_en');
            $table->string('extension');
            $table->string('status');
            $table->tinyInteger('stage_edit');
            $table->tinyInteger('contributor_stage')->default(0);
            $table->string('review_notes')->default('');
            $table->string('hash');
            $table->string('original_name');
            $table->tinyInteger('is_uploaded');
            $table->tinyInteger('has_noise');
            $table->string('token_id');
            $table->string('preview');
            $table->string('duration');
            $table->timestamps();
            $table->integer('olde_video_id')->nullable();
            $table->string('preview_admin');
            $table->bigInteger('reviewer_id')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->bigInteger('publisher_id')->nullable();
            $table->timestamp('published_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contributor_videos');
    }
}
