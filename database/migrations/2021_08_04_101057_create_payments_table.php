<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->unsignedInteger('payment_method_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('order_id');
            $table->string('payment_id');
            $table->string('txn_id')->nullable();
            $table->decimal('total');
            $table->char('currency', 5)->default('USD');
            $table->boolean('status')->default(0);
            $table->string('invoice_file');
            $table->text('data')->nullable();
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
        Schema::dropIfExists('payments');
    }
}
