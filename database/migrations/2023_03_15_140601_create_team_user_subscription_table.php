<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamUserSubscriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_user_subscription', function (Blueprint $table) {
            $table->bigInteger('subscription_id')->index();
            $table->bigInteger('team_id')->index();
            $table->bigInteger('user_id')->index();
            $table->integer('credits')->default(0);
            $table->integer('remaining_credits')->default(0);
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
        Schema::dropIfExists('team_user_subscription');
    }
}
