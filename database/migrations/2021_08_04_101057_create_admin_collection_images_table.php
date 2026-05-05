<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminCollectionImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_collection_images', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->integer('id', true);
            $table->integer('image_id')->nullable();
            $table->integer('admin_collection_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['image_id', 'admin_collection_id'], 'image_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_collection_images');
    }
}
