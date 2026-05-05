<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFolderIdContributorSubmissionIdToImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->integer('folder_id')->default(0)->index('folder_id');
            $table->integer('contributor_submission_id')->default(0)->index('contributor_submission_id');
        });
        \Illuminate\Support\Facades\DB::statement(
            "UPDATE images SET folder_id =( SELECT folder_id FROM folder_image WHERE images.id = folder_image.image_id LIMIT 1 )
            WHERE EXISTS ( SELECT * FROM folder_image WHERE folder_image.image_id = images.id)");

        \Illuminate\Support\Facades\DB::statement(
            "UPDATE images SET contributor_submission_id =( SELECT contributor_submission_id FROM contributor_image_submission_items WHERE images.contributor_image_id = contributor_image_submission_items.image_id LIMIT 1 )
                WHERE EXISTS (SELECT * FROM contributor_image_submission_items WHERE images.contributor_image_id = contributor_image_submission_items.image_id)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('folder_id');
            $table->dropColumn('contributor_submission_id');
        });
    }
}
