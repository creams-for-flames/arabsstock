<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_videos', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->unsignedInteger('video_id');
            $table->unsignedInteger('user_id');
            $table->decimal('amount');
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
        Schema::dropIfExists('cart_videos');
    }
}
