<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVectorsReportedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vectors_reporteds', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('image_id');
            $table->enum('reason', ['copyright', 'privacy_issue', 'violent_sexual_content']);
            $table->timestamp('created_at')->useCurrent();
            $table->index(['user_id', 'image_id'], 'user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vectors_reporteds');
    }
}
