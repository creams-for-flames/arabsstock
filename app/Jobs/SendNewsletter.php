<?php

namespace App\Jobs;

use App\Mail\Newsletter;
use App\Models\Contributor;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class SendNewsletter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $newsletter;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($newsletter)
    {
        $this->newsletter = $newsletter;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $newsletter = $this->newsletter;
        $users = User::select('id', 'name', 'email')->where('receive_newsletters', 1)->doesntHave('bounced_emails');
        $contributors = Contributor::select('id', 'name', 'email')->doesntHave('bounced_emails');
        if ($newsletter->receivers == 'specific')
            $users->whereIn('email', explode(',', $newsletter->specific_users));
        $backup = config('mail');
        config()->set('mail',config('newsletter.mail'));
        $index = 0;
        foreach ($users->get() as $user) {
            $index++;
            $later = now()->addMinutes(floor($index / 100));
            DB::table('newsletter_user')->insert([
                'newsletter_id' => $newsletter->id,
                'user_id' => $user->id,
                'time' => now(),
            ]);
            Mail::to($user)->later($later, new Newsletter($this->newsletter, $user));
        }
        if ($newsletter->receivers != 'specific')
            foreach ($contributors->get() as $user) {
                $index++;
                $later = now()->addMinutes(floor($index / 100));
                Mail::to($user)->later($later, new Newsletter($this->newsletter, $user));
            }
        config()->set('mail',$backup);
    }
}
