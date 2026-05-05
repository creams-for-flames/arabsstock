<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFolderIdContributorSubmissionIdToVectorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vectors', function (Blueprint $table) {
            $table->integer('folder_id')->default(0)->index('folder_id');
            $table->integer('contributor_submission_id')->default(0)->index('contributor_submission_id');
        });
        \Illuminate\Support\Facades\DB::statement(
            "UPDATE vectors SET folder_id =( SELECT folder_id FROM folder_vector WHERE vectors.id = folder_vector.vector_id LIMIT 1 )
            WHERE EXISTS ( SELECT * FROM folder_vector WHERE folder_vector.vector_id = vectors.id)");
        \Illuminate\Support\Facades\DB::statement(
            "UPDATE vectors SET contributor_submission_id =( SELECT contributor_submission_id FROM contributor_vector_submission_items WHERE vectors.contributor_vector_id = contributor_vector_submission_items.vector_id LIMIT 1 )
                WHERE EXISTS (SELECT * FROM contributor_vector_submission_items WHERE vectors.contributor_vector_id = contributor_vector_submission_items.vector_id)");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vectors', function (Blueprint $table) {
            $table->dropColumn('folder_id');
            $table->dropColumn('contributor_submission_id');
        });
    }
}
