<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWarehouseChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_checks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->nullable();
            $table->string('path')->nullable();
            $table->string('note')->nullable();
            $table->enum('status', ['notfound','pending', 'done'])->default('notfound');
            $table->integer('warehouseable_id');
            $table->string('warehouseable_type');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['warehouseable_id', 'warehouseable_type']);


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouse_checks');
    }
}
