<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImageSearchKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('image_search_keys', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->integer('id', true);
            $table->string('key_word', 191)->nullable();
            $table->string('lang', 3);
            $table->integer('count')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('image_search_keys');
    }
}
