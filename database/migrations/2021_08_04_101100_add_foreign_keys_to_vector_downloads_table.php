<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToVectorDownloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vector_downloads', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->foreign('plan_id', 'vector_downloads_ibfk_1')->references('id')->on('vector_plans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vector_downloads', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->dropForeign('vector_downloads_ibfk_1');
        });
    }
}
