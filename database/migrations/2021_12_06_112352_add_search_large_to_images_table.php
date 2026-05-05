<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSearchLargeToImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->string('search_large')->nullable()->index('image');
            $table->decimal('height_search_large')->default(0.00);
            $table->decimal('width_search_large')->default(0.00);
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
            $table->dropColumn('search_large');
            $table->dropColumn('height_search_large');
            $table->dropColumn('width_search_large');
        });
    }
}
