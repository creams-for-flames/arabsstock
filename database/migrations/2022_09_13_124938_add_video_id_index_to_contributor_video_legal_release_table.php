<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVideoIdIndexToContributorVideoLegalReleaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contributor_video_legal_release', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('contributor_video_legal_release');
            if (! $doctrineTable->hasIndex('video_id')) {
                $table->index('video_id', 'video_id');
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
        Schema::table('contributor_video_legal_release', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('contributor_video_legal_release');

            if ($doctrineTable->hasIndex('video_id')) {
                $table->dropIndex('video_id');
            }
        });
    }
}
