<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->unsignedInteger('destination');
            $table->unsignedInteger('author');
            $table->unsignedInteger('target');
            $table->enum('type', ['1', '2', '3', '4', '5', '6', '7'])->comment('1 Follow, 2  Like, 3 reply, 4 Like Comment');
            $table->enum('status', ['0', '1'])->default('0')->comment('0 unseen, 1 seen');
            $table->timestamp('created_at')->useCurrent();
            $table->enum('trash', ['0', '1'])->default('0')->index('trash')->comment('\'0 No\',\'1Yes\'');
            $table->index(['destination', 'author', 'target', 'status'], 'destination');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
