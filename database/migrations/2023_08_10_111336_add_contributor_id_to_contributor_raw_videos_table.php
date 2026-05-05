<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContributorIdToContributorRawVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contributor_raw_videos', function (Blueprint $table) {
            $table->unsignedBigInteger('contributor_id')->nullable()->after('contributor_video_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contributor_raw_videos', function (Blueprint $table) {
            $table->dropColumn(['contributor_id']);

        });
    }
}
