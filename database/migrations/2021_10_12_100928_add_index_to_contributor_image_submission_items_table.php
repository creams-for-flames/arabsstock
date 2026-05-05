<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToContributorImageSubmissionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contributor_image_submission_items', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('contributor_image_submission_items');
            if (! $doctrineTable->hasIndex('multicolumnindexname')) {
                $table->index(['contributor_submission_id', 'image_id'], 'multicolumnindexname');
            }

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contributor_image_submission_items', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('contributor_image_submission_items');

            if ($doctrineTable->hasIndex('multicolumnindexname')) {
                $table->dropIndex('multicolumnindexname');
            }

        });
    }
}
