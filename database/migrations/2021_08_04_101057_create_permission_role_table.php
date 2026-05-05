<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permission_role', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->unsignedInteger('permission_id')->nullable()->index('fk_p_273466_273467_role_p_5c7d00448cb7c');
            $table->unsignedInteger('role_id')->nullable()->index('fk_p_273467_273466_permis_5c7d00448cc9c');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permission_role');
    }
}
