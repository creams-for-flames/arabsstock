<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->unsignedInteger('user_id')->index('user_id');
            $table->unsignedInteger('city_id')->nullable();
            $table->unsignedInteger('country_id')->nullable();
            $table->unsignedInteger('plan_id')->index('subscriptions_plan_id_foreign');
            $table->unsignedInteger('payment_method_id')->index('subscriptions_payment_method_id_foreign');
            $table->string('subscription_id')->nullable();
            $table->integer('quantity')->default(1);
            $table->boolean('created_by_hook')->default(0);
            $table->integer('remaining_credits')->default(0);
            $table->string('plan_type', 20)->nullable()->comment('package | monthly | annual');
            $table->string('currency', 7)->default('USD');
            $table->decimal('amount')->default(0.00);
            $table->decimal('payment_gateway_fee', 7)->default(0.00);
            $table->string('payment_id')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('status')->default(0);
            $table->boolean('renewal')->default(0);
            $table->string('invoice_file');
            $table->text('data')->nullable();
            $table->foreign('payment_method_id', 'subscriptions_ibfk_1')->references('id')->on('payment_methods');
            $table->foreign('plan_id', 'subscriptions_ibfk_2')->references('id')->on('plans');
            $table->timestamp('start_period')->nullable();
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
        Schema::dropIfExists('subscriptions');
    }
}
