<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\WeeklyLetterClient;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;


class SendWeeklyLetter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $weekly_letter;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($weekly_letter)
    {
        $this->weekly_letter = $weekly_letter;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $weekly_letter = $this->weekly_letter;
        $weekly_letter = WeeklyLetterClient::find($weekly_letter);
        $ids = [1,2,1569];
        $users = User::select('id', 'name', 'email','receive_newsletters')->where('receive_newsletters', 1)->whereNotIn('id',$ids)->doesntHave('bounced_emails');
        if ($weekly_letter->target == 'custom')
            $users->whereIn('email', explode(',', $weekly_letter->custom_target));

        $weekly_letter->target_count = $users->count();
        $weekly_letter->status = 'submit';
        $weekly_letter->save();
        foreach ($users->get()->chunk(100) as $k => $chunk) {
            
            dispatch(new SendWeeklyLetterChunk($chunk, $weekly_letter));
            Log::channel('info')->info("SendWeeklyLetterChunk  {$k}" );
        }
    }
}
