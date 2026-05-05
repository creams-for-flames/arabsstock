<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexImageSearchKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('image_search_keys', function (Blueprint $table) {
            $table->index(['key_word', 'lang','count']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('image_search_keys', function (Blueprint $table) {
            $table->dropIndex(['key_word','lang','count']);

        });
    }
}
