<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToWeeklyLetterClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('weekly_letter_clients', function (Blueprint $table) {
            $table->enum('status',['pending','active','submit'])->default('pending');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('weekly_letter_clients', function (Blueprint $table) {
            $table->dropColumn('status');
            //
        });
    }
}
