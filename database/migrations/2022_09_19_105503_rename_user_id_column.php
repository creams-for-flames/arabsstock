<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameUserIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contributor_images', function (Blueprint $table) {
            $table->renameColumn('user_id', 'contributor_id');
        });
        Schema::table('contributor_videos', function (Blueprint $table) {
            $table->renameColumn('user_id', 'contributor_id');
        });
        Schema::table('contributor_vectors', function (Blueprint $table) {
            $table->renameColumn('user_id', 'contributor_id');
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
            $table->renameColumn('contributor_id', 'user_id');
        });
        Schema::table('contributor_videos', function (Blueprint $table) {
            $table->renameColumn('contributor_id', 'user_id');
        });
        Schema::table('contributor_vectors', function (Blueprint $table) {
            $table->renameColumn('contributor_id', 'user_id');
        });
    }
}
