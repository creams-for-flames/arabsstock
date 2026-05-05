<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActorVideoFolderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actor_video_folder', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('actor_id');
            $table->unsignedBigInteger('folder_id');
            $table->text('contract');
            $table->string('contract_file');
            $table->tinyInteger('is_uploaded')->default(0);
            $table->enum('status',['active','not_active'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actor_video_folder');
    }
}
