<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExclusiveCreditsToSectionConstTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('section_const', function (Blueprint $table) {
            $table->integer('exclusive_credits')->after('enhanced_credits');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('section_const', function (Blueprint $table) {
            $table->dropColumn('exclusive_credits');
        });
    }
}
