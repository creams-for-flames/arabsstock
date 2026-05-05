<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContributorVectorsDownloadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contributor_vectors_download', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->unsignedInteger('image_id');
            $table->unsignedInteger('contributor_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('plan_id');
            $table->decimal('plan_price')->default(0.00);
            $table->decimal('image_price')->default(0.00);
            $table->decimal('profit_ratio')->default(0.00);
            $table->timestamps();
            $table->decimal('profit_value')->default(0.00);
            $table->integer('type_download')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contributor_vectors_download');
    }
}
