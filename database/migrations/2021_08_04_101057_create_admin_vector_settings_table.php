<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminVectorSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_vector_settings', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->string('title_ar', 191)->nullable();
            $table->string('title_en', 191)->nullable();
            $table->string('title_image_ar', 500)->nullable();
            $table->string('title_image_en', 500)->nullable();
            $table->text('description_en');
            $table->text('description_ar');
            $table->string('welcome_text_en', 200);
            $table->string('welcome_text_ar', 200);
            $table->text('welcome_subtitle_en');
            $table->text('welcome_subtitle_ar');
            $table->unsignedInteger('result_request')->comment('The max number of images per request');
            $table->unsignedInteger('limit_upload_user');
            $table->enum('status_page', ['0', '1'])->default('1')->comment('0 Offline, 1 Online');
            $table->unsignedInteger('message_length');
            $table->unsignedInteger('comment_length');
            $table->enum('registration_active', ['0', '1'])->default('1')->comment('0 No, 1 Yes');
            $table->enum('email_verification', ['0', '1'])->comment('0 Off, 1 On');
            $table->string('email_no_reply', 200);
            $table->double('exchange_price')->nullable();
            $table->string('email_admin', 200);
            $table->enum('captcha', ['on', 'off'])->default('on');
            $table->unsignedInteger('file_size_allowed')->comment('Size in Bytes');
            $table->enum('facebook_login', ['on', 'off'])->default('off');
            $table->text('google_analytics');
            $table->enum('invitations_by_email', ['on', 'off'])->default('on');
            $table->string('twitter', 200);
            $table->string('facebook', 200);
            $table->string('googleplus', 200);
            $table->string('linkedin', 200);
            $table->string('instagram', 200);
            $table->text('google_adsense');
            $table->enum('auto_approve_images', ['on', 'off'])->default('off');
            $table->unsignedInteger('tags_limit');
            $table->enum('downloads', ['all', 'users'])->default('all');
            $table->enum('google_ads_index', ['on', 'off'])->default('off');
            $table->unsignedInteger('description_length');
            $table->string('min_width_height_image', 25);
            $table->text('google_adsense_index');
            $table->string('link_privacy', 200);
            $table->string('link_terms', 200);
            $table->enum('twitter_login', ['on', 'off'])->default('off');
            $table->text('keywords_en');
            $table->text('keywords_ar');
            $table->text('tags_en_in_home');
            $table->text('tags_ar_in_home');
            $table->decimal('profit_ratio')->default(0.00);
            $table->string('releas_model');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_vector_settings');
    }
}
