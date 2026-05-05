<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWarehouseCheckRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_check_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('status')->default(false);
            $table->enum('type',['images','videos','vectors']);
            $table->enum('target',['all','custom']);
            $table->text('custom_target')->nullable();
            $table->integer('target_count')->nullable(); 
            $table->integer('check_count')->nullable()->default(0); 
            $table->integer('latest_id_check')->nullable(); 
            $table->softDeletes();
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
        Schema::dropIfExists('warehouse_check_requests');
    }
}
