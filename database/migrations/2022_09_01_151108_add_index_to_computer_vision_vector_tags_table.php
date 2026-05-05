<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToComputerVisionVectorTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('computer_vision_vector_tags', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('computer_vision_vector_tags');
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
        Schema::table('computer_vision_vector_tags', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('computer_vision_vector_tags');

            if ($doctrineTable->hasIndex('vector_id')) {
                $table->dropIndex('vector_id');
            }
        });
    }
}
