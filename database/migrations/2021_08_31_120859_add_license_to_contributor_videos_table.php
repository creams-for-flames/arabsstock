<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLicenseToContributorVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contributor_videos', function (Blueprint $table) {
            $table->enum('license', ["commercial","editorial"])->default("commercial");
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
            $table->dropColumn('license');
        });
    }
}
