<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveTimestampFromPoivtCategoryContributorVectorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category_contributor_vector', function (Blueprint $table) {
            $table->dropColumn(['created_at', 'updated_at']);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category_contributor_vector', function (Blueprint $table) {
            //
        });
    }
}
