<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersReportedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_reporteds', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->enum('reason', ['copyright', 'privacy_issue', 'violent_sexual_content', 'spoofing']);
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('id_reported');
            $table->timestamp('created_at')->useCurrent();
            $table->index(['user_id', 'id_reported'], 'user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_reporteds');
    }
}
