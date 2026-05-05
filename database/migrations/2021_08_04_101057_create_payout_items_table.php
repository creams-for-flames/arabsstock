<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayoutItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payout_items', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->integer('payout_batch_tbl_id');
            $table->string('payout_batch_id');
            $table->integer('withdraw_id');
            $table->string('payout_item_id');
            $table->string('transaction_id');
            $table->string('transaction_status');
            $table->decimal('payout_item_fee');
            $table->timestamps();
            $table->enum('payout_batch_status', ['PENDING', 'DENIED', 'PROCESSING', 'SUCCESS', 'CANCELED']);
            $table->enum('withdraw_status', ['PENDING', 'SUCCESS', 'FAILED', 'UNCLAIMED', 'RETURNED', 'ONHOLD', 'BLOCKED', 'REFUNDED', 'REVERSED']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payout_items');
    }
}
