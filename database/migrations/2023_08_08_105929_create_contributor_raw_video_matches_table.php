<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContributorRawVideoMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contributor_raw_video_matches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('contributor_raw_video_id')->nullable();
            $table->unsignedBigInteger('contributor_video_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contributor_raw_video_matches');
    }
}
