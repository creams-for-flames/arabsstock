<?php

namespace App\Console\Commands;

use App\Jobs\HashContributorVideosJob;
use App\Jobs\SendEmailDailyContributorSales;
use App\Models\Contributor;
use App\Models\Purchase;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DailyContributorSales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:send_daily_contributor_sales';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Daily Contributor Sales';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $contributor_ids = Purchase::whereDate('created_at', Carbon::today())->groupBy('contributor_id')->pluck('contributor_id');
        $contributors = Contributor::whereIn('id', $contributor_ids)->doesntHave('bounced_emails')->get();
        foreach ($contributors as $contributor) {
            $total_profit_value = Purchase::with(['user'])->whereDate('created_at', Carbon::today())->where('contributor_id', $contributor->id)->sum('profit_value');
            $sales = Purchase::select('purchaseable_type', 'purchaseable_id', 'profit_value', 'contributor_id', 'user_id', DB::raw('count(*) as quantity'), DB::raw('profit_value'))->whereDate('created_at', Carbon::today())->where('contributor_id', $contributor->id)->groupby('purchaseable_type', 'purchaseable_id')->get();
            if (count($sales) > 0)
                dispatch(new SendEmailDailyContributorSales($sales, $contributor->email, $contributor->name, $total_profit_value));
        }
    }
}
