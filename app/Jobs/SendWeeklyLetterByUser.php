<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\WeeklyLetterEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;


class SendWeeklyLetterByUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $weekly_letter;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($weekly_letter,$user)
    {
        $this->user = $user;
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
        $user = $this->user;
        if (isset($user) && isset($weekly_letter) && $user->receive_newsletters) {
            $recipientEmail = strtolower($user->email);
            $backup = config('mail');
            config()->set('mail', config('newsletter.mail'));
            $unsubscribe_url = route('email.unsubscribe',['id'=>$user->id,'username'=>$user->username,'token'=>$user->token]);
    
            $weekly_letter_email =new WeeklyLetterEmail($weekly_letter,$unsubscribe_url); 
            $weekly_letter_email->to($recipientEmail);
            Mail::send($weekly_letter_email);

            Log::channel('info')->info("SendWeeklyLetterByUser Sendgrid sent : weekly_letter: {$weekly_letter->id} user: {$user->id}");
            $weekly_letter->users()->attach($user);
            if ($weekly_letter->users()->count() === $weekly_letter->target_count) {
                $weekly_letter->sent = 1;
                $weekly_letter->save();
            }
            config()->set('mail', $backup);
        }
    }
}
