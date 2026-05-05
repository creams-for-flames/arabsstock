<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableContentsChangeTitles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->string('title_ar', 500)->change();
            $table->string('title_en', 500)->change();
        });
        Schema::table('videos', function (Blueprint $table) {
            $table->string('title_ar', 500)->change();
            $table->string('title_en', 500)->change();
        });
        Schema::table('vectors', function (Blueprint $table) {
            $table->string('title_ar', 500)->change();
            $table->string('title_en', 500)->change();
        });
        Schema::table('contributor_images', function (Blueprint $table) {
            $table->string('title_ar', 500)->change();
            $table->string('title_en', 500)->change();
        });
        Schema::table('contributor_videos', function (Blueprint $table) {
            $table->string('title_ar', 500)->change();
            $table->string('title_en', 500)->change();
        });
        Schema::table('contributor_vectors', function (Blueprint $table) {
            $table->string('title_ar', 500)->change();
            $table->string('title_en', 500)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
