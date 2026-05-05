<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
//            $table->engine = 'MyISAM';
            $table->engine = 'InnoDB';
            $table->integer('id', true);
            $table->integer('contributor_image_id')->default(0)->index('contributor_image_id');
            $table->bigInteger('sort')->default(0);
            $table->string('thumbnail');
            $table->decimal('height_thumbnail')->default(0.00);
            $table->decimal('width_thumbnail')->default(0.00);
            $table->string('preview', 100)->index('image');
            $table->decimal('height_preview')->default(0.00);
            $table->decimal('width_preview')->default(0.00);
            $table->string('small');
            $table->decimal('height_small')->default(0.00);
            $table->decimal('width_small')->default(0.00);
            $table->string('medium');
            $table->decimal('height_medium')->default(0.00);
            $table->decimal('width_medium')->default(0.00);
            $table->string('large');
            $table->decimal('height_large')->default(0.00);
            $table->decimal('width_large')->default(0.00);
            $table->string('title_ar')->index('title');
            $table->string('title_en');
            $table->string('slug')->default('');
            $table->text('description_ar');
            $table->text('description_en');
            $table->unsignedInteger('categories_id')->nullable()->index('category_id');
            $table->unsignedInteger('user_id');
            $table->timestamp('date')->useCurrent();
            $table->enum('status', ['active', 'pending'])->default('pending');
            $table->string('token_id')->unique('token_id');
            $table->string('extension', 25);
            $table->text('colors');
            $table->string('exif');
            $table->string('camera');
            $table->enum('featured', ['yes', 'no'])->default('yes');
            $table->enum('how_use_image', ['free', 'free_personal', 'editorial_only', 'web_only'])->default('free');
            $table->enum('attribution_required', ['yes', 'no'])->default('yes');
            $table->string('original_name');
            $table->tinyInteger('in_home')->default(1);
            $table->integer('category_admin_id')->nullable();
            $table->string('hash')->default('');
            $table->dateTime('featured_date')->nullable();
            $table->tinyInteger('stage_edit')->nullable()->default(0)->comment('0 no edit / 1 smie edit 2 full edit');
            $table->dateTime('updated_at')->nullable();
            $table->softDeletes();
            $table->integer('count_view');
            $table->bigInteger('reviewer_id')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->bigInteger('publisher_id')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->index(['user_id', 'status', 'token_id'], 'author_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
}
