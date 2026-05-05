<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVectorSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vector_subscriptions', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('city_id')->nullable();
            $table->unsignedInteger('country_id')->nullable();
            $table->unsignedInteger('plan_id')->index('subscriptions_plan_id_foreign');
            $table->unsignedInteger('payment_method_id')->index('subscriptions_payment_method_id_foreign');
            $table->string('subscription_id')->nullable();
            $table->integer('quantity')->default(1);
            $table->boolean('created_by_hook')->default(0);
            $table->integer('download_remaining')->default(0);
            $table->string('plan_type', 20)->nullable()->comment('package | monthly');
            $table->string('currency', 7)->default('USD');
            $table->decimal('amount', 7)->default(0.00);
            $table->decimal('payment_gateway_fee', 7)->default(0.00);
            $table->string('payment_id')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('status')->default(0);
            $table->boolean('renewal')->default(0);
            $table->string('invoice_file');
            $table->text('data')->nullable();
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
        Schema::dropIfExists('vector_subscriptions');
    }
}
