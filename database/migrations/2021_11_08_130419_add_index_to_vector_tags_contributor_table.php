<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToVectorTagsContributorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vectors_tags_contributor', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('vectors_tags_contributor');
            if (! $doctrineTable->hasIndex('vector_id')) {
                $table->index('vector_id', 'vector_id');
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
        Schema::table('vectors_tags_contributor', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('vectors_tags_contributor');

            if ($doctrineTable->hasIndex('vector_id')) {
                $table->dropIndex('vector_id');
            }
        });
    }
}
