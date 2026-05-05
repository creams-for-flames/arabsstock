<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImageIdIndexToContributorImageLegalReleaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contributor_image_legal_release', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('contributor_image_legal_release');
            if (! $doctrineTable->hasIndex('image_id')) {
                $table->index('image_id', 'image_id');
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
        Schema::table('contributor_image_legal_release', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('contributor_image_legal_release');

            if ($doctrineTable->hasIndex('image_id')) {
                $table->dropIndex('image_id');
            }
        });
    }
}
