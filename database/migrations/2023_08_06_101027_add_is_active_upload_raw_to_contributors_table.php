<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsActiveUploadRawToContributorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contributors', function (Blueprint $table) {
            $table->tinyInteger('is_active_upload_raw')->default(0)->comment('use it for add role upload raw video');
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
            $table->dropColumn(['is_active_upload_raw']);

        });
    }
}
