<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoCommentLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_comment_likes', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('comment_id');
            $table->enum('status', ['0', '1'])->default('1')->comment('0 trash, 1 active');
            $table->timestamp('created_at')->useCurrent();
            $table->index(['user_id', 'comment_id'], 'user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_comment_likes');
    }
}
