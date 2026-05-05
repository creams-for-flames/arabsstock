<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStripePlanToImagePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('image_plans', function (Blueprint $table) {
            $table->string('stripe_plan')->nullable()->after('paypal_plan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('image_plans', function (Blueprint $table) {
            $table->dropColumn('stripe_plan');
        });
    }
}
