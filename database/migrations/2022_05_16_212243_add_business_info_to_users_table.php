<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBusinessInfoToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_business')->default(false);
            $table->string('company_name')->nullable();
            $table->string('company_address')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_tax_id')->nullable();
            $table->string('company_phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_business');
            $table->dropColumn('company_name');
            $table->dropColumn('company_address');
            $table->dropColumn('company_email');
            $table->dropColumn('company_tax_id');
            $table->dropColumn('company_phone');
        });
    }
}
