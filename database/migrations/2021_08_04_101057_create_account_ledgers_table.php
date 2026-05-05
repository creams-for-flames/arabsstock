<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_ledgers', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->enum('proccess', ['pay', 'withdraw']);
            $table->decimal('value');
            $table->integer('contributor_id');
            $table->integer('accountable_id');
            $table->string('accountable_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_ledgers');
    }
}
