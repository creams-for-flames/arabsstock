<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContributorImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contributor_images', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->string('title_en');
            $table->string('title_ar');
            $table->string('thumbnail');
            $table->unsignedInteger('user_id');
            $table->string('original_name');
            $table->string('large');
            $table->string('extension');
            $table->integer('contributor_stage');
            $table->string('review_notes')->default('');
            $table->string('hash');
            $table->tinyInteger('stage_edit')->default(0);
            $table->string('comment');
            $table->timestamps();
            $table->string('preview')->nullable();
            $table->integer('olde_image_id')->nullable();
            $table->bigInteger('reviewer_id')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->bigInteger('publisher_id')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->tinyInteger('is_uploaded');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contributor_images');
    }
}
