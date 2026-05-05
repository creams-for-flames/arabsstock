<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendWeeklyLetterChunk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $chunk;
    protected $weekly_letter;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($chunk, $weekly_letter)
    {
        $this->chunk = $chunk;
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
        foreach ($this->chunk as $user){
            dispatch(new SendWeeklyLetterByUser($weekly_letter,$user))->onQueue('weeklyletter');
             Log::channel('info')->info("SendWeeklyLetterChunk: weekly_letter: {$weekly_letter->id} user: {$user->id}" );
        }

        
    }

}
