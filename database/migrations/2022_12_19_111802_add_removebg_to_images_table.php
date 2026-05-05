<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRemovebgToImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->string('removebg_image',255)->nullable();
            $table->decimal('height_removebg')->default(0.00);
            $table->decimal('width_removebg')->default(0.00);
            $table->enum('removebg_status',['queue','processing','done'])->nullable();
            $table->enum('removebg_status_disply',['pending','active'])->default('pending');
            $table->enum('removebg_type',['free','paid'])->default('free');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('removebg_image','height_removebg','width_removebg','removebg_status','removebg_type');
            
        });
    }
}
