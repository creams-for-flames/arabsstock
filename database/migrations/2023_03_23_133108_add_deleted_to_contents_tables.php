<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeletedToContentsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE images MODIFY COLUMN `status` ENUM('active','pending','error','deleted')");
        DB::statement("ALTER TABLE videos MODIFY COLUMN `status` ENUM('active','pending','error','deleted')");
        DB::statement("ALTER TABLE vectors MODIFY COLUMN `status` ENUM('active','pending','error','deleted')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE images MODIFY COLUMN `status` ENUM('active','pending','error')");
        DB::statement("ALTER TABLE videos MODIFY COLUMN `status` ENUM('active','pending','error')");
        DB::statement("ALTER TABLE vectors MODIFY COLUMN `status` ENUM('active','pending','error')");
    }
}
