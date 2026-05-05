<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRemovebgPreviewToImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->string('removebg_preview',500)->nullable()->index('image_removebg_preview_index');
            $table->decimal('height_removebg_preview')->default(0.00);
            $table->decimal('width_removebg_preview')->default(0.00);
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
            $table->dropColumn('removebg_preview');
            $table->dropColumn('height_removebg_preview');
            $table->dropColumn('width_removebg_preview');
        });
    }
}
