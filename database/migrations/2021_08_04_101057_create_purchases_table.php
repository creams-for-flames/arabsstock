<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->unsignedInteger('purchaseable_id');
            $table->string('purchaseable_type');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('contributor_id');
            $table->unsignedInteger('plan_id')->nullable();
            $table->decimal('plan_price')->nullable()->default(1.00);
            $table->decimal('unit_price');
            $table->decimal('profit_ratio');
            $table->decimal('profit_value');
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
        Schema::dropIfExists('purchases');
    }
}
