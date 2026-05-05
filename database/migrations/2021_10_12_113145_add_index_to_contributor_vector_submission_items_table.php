<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToContributorVectorSubmissionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contributor_vector_submission_items', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('contributor_vector_submission_items');
            if (! $doctrineTable->hasIndex('multicolumnindexname')) {
                $table->index(['contributor_submission_id', 'vector_id'], 'multicolumnindexname');
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
        Schema::table('contributor_vector_submission_items', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('contributor_vector_submission_items');

            if ($doctrineTable->hasIndex('multicolumnindexname')) {
                $table->dropIndex('multicolumnindexname');
            }
        });
    }
}
