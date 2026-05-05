<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToVectorSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vector_subscriptions', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->foreign('plan_id', 'vector_subscriptions_ibfk_1')->references('id')->on('vector_plans');
            $table->foreign('payment_method_id', 'vector_subscriptions_ibfk_2')->references('id')->on('payment_methods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vector_subscriptions', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->dropForeign('vector_subscriptions_ibfk_1');
            $table->dropForeign('vector_subscriptions_ibfk_2');
        });
    }
}
