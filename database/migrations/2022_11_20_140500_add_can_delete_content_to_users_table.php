<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCanDeleteContentToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('can_delete_images')->default(0);
            $table->tinyInteger('can_delete_videos')->default(0);
            $table->tinyInteger('can_delete_vectors')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('can_delete_images');
            $table->dropColumn('can_delete_videos');
            $table->dropColumn('can_delete_vectors');
        });
    }
}
