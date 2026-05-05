<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('session_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->decimal('cost', 10, 2);
            $table->string('file');
            $table->unsignedBigInteger('invoiceable_id'); 
            $table->string('invoiceable_type'); 
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('session_invoices');
    }
}
