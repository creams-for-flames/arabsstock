<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContributorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contributors', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->string('username');
            $table->string('name');
            $table->string('bio');
            $table->integer('country_id');
            $table->integer('city_id');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('api_token');
            $table->string('avatar');
            $table->string('website');
            $table->string('twitter');
            $table->enum('status', ['pending', 'active', 'suspended', 'delete']);
            $table->string('paypal_account');
            $table->double('profit_ratio', 8, 2);
            $table->decimal('total_amount')->default(0.00);
            $table->rememberToken();
            $table->timestamps();
            $table->string('mobile')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contributors');
    }
}
