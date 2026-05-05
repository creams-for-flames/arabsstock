<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoryToRejectionReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rejection_reasons', function (Blueprint $table) {
            $table->enum('category',['images','videos','vectors'])->default('images');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rejection_reasons', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
}
