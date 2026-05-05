<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_videos', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->unsignedInteger('video_id');
            $table->string('name');
            $table->enum('type', ['small', 'medium', 'large']);
            $table->string('extension', 25);
            $table->string('resolution', 100);
            $table->string('size', 50);
            $table->string('token', 200)->index('token');
            $table->index(['video_id', 'type'], 'id_shot');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_videos');
    }
}
