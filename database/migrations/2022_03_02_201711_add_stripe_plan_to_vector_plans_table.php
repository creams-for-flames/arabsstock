<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStripePlanToVectorPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vector_plans', function (Blueprint $table) {
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
        Schema::table('vector_plans', function (Blueprint $table) {
            $table->dropColumn('stripe_plan');
        });
    }
}
