<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttatchmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attatchments', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->integer('id', true);
            $table->integer('attatchmentable_id');
            $table->string('attatchmentable_type', 225);
            $table->string('image', 225);
            $table->integer('type')->nullable();
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
        Schema::dropIfExists('attatchments');
    }
}
