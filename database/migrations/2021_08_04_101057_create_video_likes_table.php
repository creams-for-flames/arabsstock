<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_likes', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('video_id');
            $table->enum('status', ['0', '1'])->default('1')->comment('0 trash, 1 active');
            $table->timestamp('date')->useCurrent();
            $table->index(['user_id', 'video_id', 'status'], 'id_usr');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_likes');
    }
}
