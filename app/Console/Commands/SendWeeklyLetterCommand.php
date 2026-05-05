<?php

namespace App\Console\Commands;

use App\Jobs\HashContributorVideosJob;
use App\Jobs\SendEmailDailyContributorSales;
use App\Jobs\SendWeeklyLetter;
use App\Models\Contributor;
use App\Models\Purchase;
use App\Models\WeeklyLetter;
use App\Models\WeeklyLetterClient;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendWeeklyLetterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:send_weekly_letter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Weekly Letter';

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
        /* 
         this  command disabled
        */
        $WeeklyLetters = WeeklyLetterClient::with('files.fileable')->where('sent',false)->whereHas('files',function($q){
            $q->where('image_generated',true);
        })->get();
        foreach ($WeeklyLetters as $weeklyLetter){
            SendWeeklyLetter::dispatch($weeklyLetter);
        }
    }
}
