<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_plans', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->string('braintree_plan', 191)->nullable();
            $table->string('paypal_plan', 191)->nullable();
            $table->string('title_en')->nullable()->default('NULL');
            $table->string('title_ar');
            $table->string('slug', 191)->nullable();
            $table->string('uuid', 191)->nullable();
            $table->decimal('price', 15)->nullable();
            $table->unsignedInteger('downloads_count')->nullable();
            $table->string('type')->nullable();
            $table->tinyInteger('status')->nullable()->default(1);
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
        Schema::dropIfExists('video_plans');
    }
}
