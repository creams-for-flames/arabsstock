<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->string('title_en')->nullable();
            $table->text('description_en')->nullable();
            $table->string('title_ar')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('image')->nullable();
            $table->string('link')->nullable();
            $table->string('status')->nullable();
            $table->string('email')->nullable();
            $table->string('key')->nullable();
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
        Schema::dropIfExists('payment_methods');
    }
}
