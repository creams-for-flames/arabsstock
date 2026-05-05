<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImageDownloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('image_downloads', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->bigIncrements('id');
            $table->unsignedInteger('image_id')->index('publicacion_id');
            $table->unsignedInteger('user_id')->index('usr_id');
            $table->string('ip', 25)->index('ip');
            $table->string('user_plan_id', 191)->nullable();
            $table->timestamp('date')->useCurrent();
            $table->unsignedInteger('plan_id')->nullable()->index('downloads_plan_id_foreign');
            $table->unsignedInteger('subscription_id')->nullable()->index('downloads_subscription_id_foreign');
            $table->timestamps();
            $table->integer('olde_user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('image_downloads');
    }
}
