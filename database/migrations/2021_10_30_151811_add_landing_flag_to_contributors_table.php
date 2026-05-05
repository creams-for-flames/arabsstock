<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLandingFlagToContributorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contributors', function (Blueprint $table) {
            $table->boolean('show_land_images')->default(true);
            $table->boolean('show_land_vectors')->default(true);
            $table->boolean('show_land_videos')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contributors', function (Blueprint $table) {
            $table->dropColumn('show_land_images');
            $table->dropColumn('show_land_vectors');
            $table->dropColumn('show_land_videos');
        });
    }
}
