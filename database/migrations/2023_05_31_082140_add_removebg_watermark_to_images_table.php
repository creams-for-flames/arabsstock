<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRemovebgWatermarkToImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->string('removebg_watermark')->nullable()->index('image_removebg_watermark_index');
            $table->decimal('height_removebg_watermark')->default(0.00);
            $table->decimal('width_removebg_watermark')->default(0.00);
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
            $table->dropColumn('removebg_watermark');
            $table->dropColumn('height_removebg_watermark');
            $table->dropColumn('width_removebg_watermark');
        });
    }
}
