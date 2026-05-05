<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoDownloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_downloads', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->bigIncrements('id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('video_id')->index('publicacion_id');
            $table->string('child_id')->nullable();
            $table->string('plan_id')->nullable();
            $table->string('subscription_id')->nullable();
            $table->string('type')->nullable();
            $table->unsignedInteger('user_id')->index('usr_id');
            $table->string('ip', 25)->index('ip');
            $table->timestamp('date')->useCurrent();
            $table->integer('duplicate')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_downloads');
    }
}
