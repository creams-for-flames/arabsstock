<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOriginalTypeToContributorVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contributor_videos', function (Blueprint $table) {
            $table->enum('original_type',['normal','raw'])->default('normal')->after('contributor_id')->index()->comment('video original type');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contributor_videos', function (Blueprint $table) {
            $table->dropColumn(['original_type']);
        });
    }
}
