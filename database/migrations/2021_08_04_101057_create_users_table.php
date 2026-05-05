<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->string('username', 30);
            $table->string('name', 50);
            $table->string('bio', 200);
            $table->unsignedInteger('country_id');
            $table->unsignedInteger('city_id')->nullable();
            $table->char('password', 60);
            $table->string('api_token');
            $table->string('email', 190)->unique();
            $table->timestamp('date')->useCurrent();
            $table->string('avatar', 70);
            $table->string('cover');
            $table->enum('status', ['pending', 'active', 'suspended', 'delete'])->default('pending');
            $table->enum('type_account', ['1', '2'])->default('1')->comment('1 Buyer, 2 Seller');
            $table->enum('role', ['normal', 'admin', 'admin_video', 'editor_image', 'editor_video', 'admin_vector', 'admin_super', 'admin_models', 'admin_image_editor', 'admin_video_editor', 'admin_vector_editor'])->default('normal')->index('role');
            $table->string('website');
            $table->rememberToken();
            $table->string('twitter', 200);
            $table->string('facebook', 200);
            $table->string('google', 200);
            $table->string('paypal_account', 200);
            $table->string('activation_code', 150)->index('activation_code');
            $table->string('oauth_uid', 200)->nullable();
            $table->string('oauth_provider', 200)->nullable();
            $table->string('token', 80);
            $table->enum('authorized_to_upload', ['yes', 'no'])->default('yes');
            $table->string('instagram', 200);
            $table->string('braintree_id')->nullable();
            $table->string('paypal_email')->nullable();
            $table->string('card_brand')->nullable();
            $table->string('card_last_four')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->tinyInteger('receive_newsletters')->default(1);
            $table->string('mobile')->nullable();
            $table->index(['username', 'status'], 'username');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
