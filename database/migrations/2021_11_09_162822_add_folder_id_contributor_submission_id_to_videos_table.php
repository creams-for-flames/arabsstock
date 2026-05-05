<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFolderIdContributorSubmissionIdToVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->integer('folder_id')->default(0)->index('folder_id');
            $table->integer('contributor_submission_id')->default(0)->index('contributor_submission_id');
        });
        \Illuminate\Support\Facades\DB::statement(
            "UPDATE videos SET folder_id =( SELECT folder_id FROM folder_video WHERE videos.id = folder_video.video_id LIMIT 1 )
            WHERE EXISTS ( SELECT * FROM folder_video WHERE folder_video.video_id = videos.id)");
        \Illuminate\Support\Facades\DB::statement(
            "UPDATE videos SET contributor_submission_id =( SELECT contributor_submission_id FROM contributor_video_submission_items WHERE videos.contributor_video_id = contributor_video_submission_items.video_id LIMIT 1 )
                WHERE EXISTS (SELECT * FROM contributor_video_submission_items WHERE videos.contributor_video_id = contributor_video_submission_items.video_id)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn('folder_id');
            $table->dropColumn('contributor_submission_id');
        });
    }
}
