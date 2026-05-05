<?php

namespace App\Jobs;

use App\Mail\DailyContributorSales;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendEmailDailyContributorSales implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $sales;
    protected $contributor_email;
    protected $contributor_name;
    protected $total_profit_value;
    public function __construct($sales,$contributor_email,$contributor_name,$total_profit_value)
    {
        $this->sales = $sales->toArray();
        $this->contributor_email = $contributor_email;
        $this->contributor_name = $contributor_name;
        $this->total_profit_value = $total_profit_value;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Illuminate\Support\Facades\Mail::queue(new DailyContributorSales($this->sales,$this->contributor_email,$this->total_profit_value,$this->contributor_name));
    }
}
