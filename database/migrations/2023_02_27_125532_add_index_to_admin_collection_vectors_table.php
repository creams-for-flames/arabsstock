<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToAdminCollectionVectorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_collection_vectors', function (Blueprint $table) {
            $table->index('vector_id');
            $table->index('admin_collection_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_collection_vectors', function (Blueprint $table) {
            $table->dropIndex(['vector_id','admin_collection_id']);

        });
    }
}
