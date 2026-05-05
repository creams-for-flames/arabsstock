<?php

namespace App\Jobs;

use App\Models\BouncedEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\EmailAlertContributorFilePublish;
use Mail;

class SendEmailAlertContributorFilePublish implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mail = $this->data['contributor']->email;
        if (BouncedEmail::where('email', $mail)->count())
            return;
        $email = new EmailAlertContributorFilePublish($this->data);
        Mail::to($mail)->send($email);
    }
}
