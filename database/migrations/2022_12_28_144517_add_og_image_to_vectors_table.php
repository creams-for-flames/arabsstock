<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOgImageToVectorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vectors', function (Blueprint $table) {
            $table->string('og_image')->nullable()->after('width_large');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vectors', function (Blueprint $table) {
            $table->dropColumn('og_image');
        });
    }
}
