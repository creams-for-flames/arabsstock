<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddRoleDesignerToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            DB::statement("ALTER TABLE users MODIFY role ENUM('normal','admin','admin_video','editor_image','editor_video','admin_vector','admin_super','admin_models','admin_image_editor','admin_video_editor','admin_vector_editor','accountant','designer') DEFAULT 'normal'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
            DB::statement("ALTER TABLE users MODIFY role ENUM('normal','admin','admin_video','editor_image','editor_video','admin_vector','admin_super','admin_models','admin_image_editor','admin_video_editor','admin_vector_editor','accountant') DEFAULT 'normal'");
    }
}
