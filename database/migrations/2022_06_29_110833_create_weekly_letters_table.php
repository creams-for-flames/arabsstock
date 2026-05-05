<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeeklyLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weekly_letters', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->unsignedBigInteger('image_1');
            $table->unsignedBigInteger('image_2');
            $table->unsignedBigInteger('image_3');
            $table->unsignedBigInteger('image_4');
            $table->unsignedBigInteger('image_5');
            $table->unsignedBigInteger('image_6');
            $table->string('cat_1_name');
            $table->unsignedBigInteger('cat_1_image');
            $table->string('cat_1_url');
            $table->string('cat_2_name');
            $table->unsignedBigInteger('cat_2_image');
            $table->string('cat_2_url');
            $table->string('cat_3_name');
            $table->unsignedBigInteger('cat_3_image');
            $table->string('cat_3_url');
            $table->string('cat_4_name');
            $table->unsignedBigInteger('cat_4_image');
            $table->string('cat_4_url');
            $table->string('cat_5_name');
            $table->unsignedBigInteger('cat_5_image');
            $table->string('cat_5_url');
            $table->string('cat_6_name');
            $table->unsignedBigInteger('cat_6_image');
            $table->string('cat_6_url');
            $table->boolean('sent')->default(false);
            $table->enum('target',['all','custom']);
            $table->text('custom_target')->nullable();
            $table->integer('target_count')->nullable();
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
        Schema::dropIfExists('weekly_letters');
    }
}
