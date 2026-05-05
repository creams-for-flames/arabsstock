<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
//            $table->engine = 'MyISAM';
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('contributor_video_id')->default(0)->index('contributor_video_id');
            $table->bigInteger('sort')->default(0);
            $table->unsignedInteger('parent_id')->nullable()->index('parent_id');
            $table->string('thumbnail');
            $table->integer('thumbnail_width');
            $table->integer('thumbnail_height');
            $table->string('cut_video')->nullable();
            $table->string('gif_video', 191)->nullable();
            $table->integer('gif_video_width');
            $table->integer('gif_video_height');
            $table->string('size_240p');
            $table->integer('size_240p_width');
            $table->integer('size_240p_height');
            $table->tinyInteger('video_fail')->nullable()->default(0);
            $table->double('price')->nullable()->default(0);
            $table->string('preview')->index('image');
            $table->string('title_ar')->index('title');
            $table->string('title_en');
            $table->string('slug')->default('');
            $table->string('type');
            $table->string('duration');
            $table->string('description_ar');
            $table->string('description_en');
            $table->unsignedInteger('categories_id')->nullable()->index('category_id');
            $table->unsignedInteger('user_id');
            $table->timestamp('date')->useCurrent();
            $table->enum('status', ['active', 'pending'])->default('pending');
            $table->string('token_id')->unique();
            $table->string('extension', 25);
            $table->string('size', 25);
            $table->string('width', 25);
            $table->string('height', 25);
            $table->string('aspect_ratio');
            $table->integer('fps');
            $table->enum('how_use_image', ['free', 'free_personal', 'editorial_only', 'web_only'])->nullable()->default('free');
            $table->tinyInteger('stage_edit')->nullable()->default(0);
            $table->integer('category_admin_id');
            $table->string('hash')->default('');
            $table->enum('attribution_required', ['yes', 'no'])->nullable()->default('yes');
            $table->string('original_name');
            $table->integer('category_video_admin_id')->nullable();
            $table->tinyInteger('is_uploaded')->nullable()->default(0)->index('is_uploaded');
            $table->boolean('has_noise')->default(0)->comment('check if video has sound or not if has sound value with be 1 ');
            $table->softDeletes();
            $table->integer('count_view');
            $table->bigInteger('reviewer_id')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->bigInteger('publisher_id')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->index(['title_en', 'description_en'], 'title_en');
            $table->index(['title_ar', 'description_ar'], 'title_ar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('videos');
    }
}
