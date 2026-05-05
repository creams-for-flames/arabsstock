<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments_logs', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('payment_method_id');
            $table->string('webhook_id')->nullable();
            $table->string('event_type')->nullable();
            $table->string('resource_type')->nullable();
            $table->string('type')->nullable();
            $table->string('category')->nullable()->comment('photos|videos');
            $table->unsignedInteger('order_id')->nullable();
            $table->unsignedInteger('subscription_id')->nullable();
            $table->unsignedInteger('plan_id')->nullable();
            $table->text('data')->nullable();
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
        Schema::dropIfExists('payments_logs');
    }
}
