<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDownloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('downloads', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->bigIncrements('id');
            $table->unsignedInteger('entity_id')->index('entity_id');
            $table->string('entity_type', 25)->index('entity_type');
            $table->integer('credits')->nullable();
            $table->string('license_type')->default('standard')->nullable();
            $table->unsignedInteger('user_id')->index('usr_id');
            $table->string('ip', 25)->index('ip');
            $table->timestamp('date')->useCurrent();
            $table->unsignedInteger('section_const_id')->index('subscriptions_section_const_id_foreign');
            $table->foreign('section_const_id', 'section_const_ibfk_2')->references('id')->on('section_const');
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
        Schema::dropIfExists('downloads');
    }
}
