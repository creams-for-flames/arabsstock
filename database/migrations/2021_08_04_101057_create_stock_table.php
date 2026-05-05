<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->unsignedInteger('image_id');
            $table->string('name');
            $table->enum('type', ['small', 'medium', 'large']);
            $table->string('extension', 25);
            $table->string('resolution', 100);
            $table->string('size', 50);
            $table->string('token', 200)->index('token');
            $table->tinyInteger('is_uploaded')->nullable()->default(0);
            $table->string('hash')->index();
            $table->integer('dpi')->nullable();
            $table->index(['image_id', 'type'], 'id_shot');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock');
    }
}
