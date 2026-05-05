<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToContributorRawVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contributor_raw_videos', function (Blueprint $table) {
            $table->index('contributor_video_id');
            $table->index('contributor_stage');

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
            $table->dropIndex(['contributor_video_id', 'contributor_stage']);

        });
    }
}
