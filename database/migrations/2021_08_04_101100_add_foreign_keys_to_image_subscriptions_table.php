<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToImageSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('image_subscriptions', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->foreign('payment_method_id', 'image_subscriptions_ibfk_1')->references('id')->on('payment_methods');
            $table->foreign('plan_id', 'image_subscriptions_ibfk_2')->references('id')->on('image_plans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('image_subscriptions', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->dropForeign('image_subscriptions_ibfk_1');
            $table->dropForeign('image_subscriptions_ibfk_2');
        });
    }
}
