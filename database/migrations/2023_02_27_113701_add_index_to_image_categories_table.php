<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToImageCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('image_categories', function (Blueprint $table) {
            $table->index('name_ar');
            $table->index('name_en');
            $table->index('thumbnail');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('image_categories', function (Blueprint $table) {
            $table->dropIndex(['name_ar','name_en', 'thumbnail']);

        });
    }
}
