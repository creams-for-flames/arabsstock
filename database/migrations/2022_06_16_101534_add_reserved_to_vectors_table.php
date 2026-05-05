<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReservedToVectorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vectors', function (Blueprint $table) {
            $table->bigInteger('reserved_to')->index();
            $table->timestamp('reserved_until')->nullable();
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
            $table->dropColumn('reserved_to');
            $table->dropColumn('reserved_until');
        });
    }
}
