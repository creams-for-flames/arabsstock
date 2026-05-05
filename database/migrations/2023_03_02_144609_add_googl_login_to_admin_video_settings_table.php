<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGooglLoginToAdminVideoSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_video_settings', function (Blueprint $table) {
            $table->enum('google_login', ['on', 'off'])->default('on');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_video_settings', function (Blueprint $table) {
            $table->dropColumn('google_login');
        });
    }
}
