<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUnitPriceToDownloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('downloads', function (Blueprint $table) {
            $table->decimal('unit_price')->after('date');
        });
        foreach (\App\Models\Download::all() as $download) {
            $unit_price = 0;
            foreach ($download->subscriptions()->with('plan')->get() as $subscription) {
                $unit_price += $subscription->credit_price * $subscription->pivot->credits;
            }
            $download->update(['unit_price' => $unit_price]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('downloads', function (Blueprint $table) {
            $table->dropColumn('unit_price');
        });
    }
}
