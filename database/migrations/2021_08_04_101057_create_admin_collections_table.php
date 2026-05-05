<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_collections', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->integer('id', true);
            $table->string('title', 191)->nullable();
            $table->integer('user_id')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('status')->nullable()->default(0);
            $table->enum('in_random_home', ['0', '1'])->default('0');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_collections');
    }
}
