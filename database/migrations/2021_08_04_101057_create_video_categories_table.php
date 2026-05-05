<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_categories', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('slug', 200)->index('slug');
            $table->string('thumbnail', 150);
            $table->string('cover', 191)->nullable();
            $table->tinyInteger('in_home')->nullable();
            $table->enum('in_random_home_video', ['0', '1'])->default('1');
            $table->integer('sort')->default(99999);
            $table->enum('cities_and_landmarks', ['on', 'off'])->nullable();
            $table->enum('mode', ['on', 'off'])->default('on');
            $table->tinyInteger('is_uploaded')->nullable()->default(0);
            $table->boolean('show_in_trending_list')->default(0);
            $table->timestamps();
            $table->enum('people', ['0', '1'])->default('0');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_categories');
    }
}
