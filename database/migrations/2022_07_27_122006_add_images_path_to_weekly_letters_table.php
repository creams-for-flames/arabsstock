<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImagesPathToWeeklyLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('weekly_letters', function (Blueprint $table) {
            $table->string('image_1_path')->nullable();
            $table->string('image_2_path')->nullable();
            $table->string('image_3_path')->nullable();
            $table->string('image_4_path')->nullable();
            $table->string('image_5_path')->nullable();
            $table->string('image_6_path')->nullable();
            $table->string('cat_1_image_path')->nullable();
            $table->string('cat_2_image_path')->nullable();
            $table->string('cat_3_image_path')->nullable();
            $table->string('cat_4_image_path')->nullable();
            $table->string('cat_5_image_path')->nullable();
            $table->string('cat_6_image_path')->nullable();
            $table->boolean('image_generated')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('weekly_letters', function (Blueprint $table) {
            $table->dropColumn('image_1_path');
            $table->dropColumn('image_2_path');
            $table->dropColumn('image_3_path');
            $table->dropColumn('image_4_path');
            $table->dropColumn('image_5_path');
            $table->dropColumn('image_6_path');
            $table->dropColumn('cat_1_image_path');
            $table->dropColumn('cat_2_image_path');
            $table->dropColumn('cat_3_image_path');
            $table->dropColumn('cat_4_image_path');
            $table->dropColumn('cat_5_image_path');
            $table->dropColumn('cat_6_image_path');
            $table->dropColumn('image_generated');
        });
    }
}
