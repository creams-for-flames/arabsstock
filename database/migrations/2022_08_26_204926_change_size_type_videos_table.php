<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeSizeTypeVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement("update videos set size=size*1");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `videos` MODIFY COLUMN `size` bigint(50) NOT NULL AFTER `extension`;");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
