<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToVideoFoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('video_folders', function (Blueprint $table) {
            $table->timestamp('session_date');
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('city_id');
            $table->text('notes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('video_folders', function (Blueprint $table) {
            $table->dropColumn(['session_date','country_id','city_id','notes']);

        });
    }
}
