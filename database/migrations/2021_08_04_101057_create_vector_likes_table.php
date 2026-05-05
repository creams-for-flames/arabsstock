<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVectorLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vector_likes', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('vector_id');
            $table->enum('status', ['0', '1'])->default('1')->comment('0 trash, 1 active');
            $table->timestamp('date')->useCurrent();
            $table->index(['user_id', 'vector_id', 'status'], 'id_usr');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vector_likes');
    }
}
