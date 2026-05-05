<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRawVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raw_videos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('video_id')->index();
            $table->string('original',500);
            $table->tinyInteger('is_uploaded_original')->nullable()->default(0)->index('is_uploaded_original');
            $table->string('preview',500)->index('preview');
            $table->tinyInteger('is_uploaded_preview')->nullable()->default(0)->index('is_uploaded_preview');
            $table->string('duration');
            $table->enum('status', ['active', 'pending'])->default('pending');
            $table->string('extension', 25);
            $table->string('size', 25);
            $table->string('hash')->default('');
            $table->string('original_name');
            $table->string('review_notes')->default('');
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
        Schema::dropIfExists('raw_videos');
    }
}
