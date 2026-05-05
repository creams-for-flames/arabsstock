<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToContributorImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contributor_images', function (Blueprint $table) {
            $table->index('id');
            $table->index('contributor_stage');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contributor_images', function (Blueprint $table) {
            $table->dropIndex('contributor_images_id_index');
            $table->dropIndex('contributor_images_contributor_stage_index');
            $table->dropIndex('contributor_images_deleted_at_index');

        });
    }
}
