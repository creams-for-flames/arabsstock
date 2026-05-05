<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWarehouseCheckRequestIdToWarehouseChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehouse_checks', function (Blueprint $table) {
           $table->integer('warehouse_check_request_id')->after('id')->unsigned()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warehouse_checks', function (Blueprint $table) {
            $table->dropColumn(['warehouse_check_request_id']);
        });
    }
}
