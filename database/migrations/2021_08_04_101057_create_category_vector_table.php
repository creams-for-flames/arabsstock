<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryVectorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_vector', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->integer('id', true);
            $table->integer('vector_id')->nullable()->index('vector_id');
            $table->integer('category_id')->nullable()->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category_vector');
    }
}
