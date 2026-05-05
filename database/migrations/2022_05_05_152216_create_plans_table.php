<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->string('paypal_plan', 191)->nullable();
            $table->string('stripe_plan')->nullable();
            $table->string('title_en')->nullable()->default('NULL');
            $table->string('title_ar');
            $table->string('slug', 191)->nullable();
            $table->string('uuid', 191)->nullable();
            $table->decimal('price', 15)->nullable();
            $table->unsignedInteger('credits_count')->nullable();
            $table->string('type')->nullable()->comment('package | monthly | annual');
            $table->tinyInteger('in_show_page')->default(0);
            $table->tinyInteger('status')->nullable()->default(1);
            $table->tinyInteger('free')->default(0);
            $table->tinyInteger('can_cancel')->default(0);
            $table->tinyInteger('is_default')->default(0);
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
        Schema::dropIfExists('plans');
    }
}
