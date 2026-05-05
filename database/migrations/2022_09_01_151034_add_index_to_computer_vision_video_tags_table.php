<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToComputerVisionVideoTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('computer_vision_video_tags', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('computer_vision_video_tags');
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
        Schema::table('computer_vision_video_tags', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('computer_vision_video_tags');

            if ($doctrineTable->hasIndex('video_id')) {
                $table->dropIndex('video_id');
            }
        });
    }
}
