<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaypalPayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paypal_payers', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->string('payer_id')->index();
            $table->string('subscription_id')->index();
            $table->integer('user_id')->index();
            $table->integer('plan_id')->index();
            $table->string('resource')->nullable();
            $table->string('ip');
            $table->integer('status')->default(0)->index();
            $table->string('notes')->default('');
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
        Schema::dropIfExists('paypal_payers');
    }
}
