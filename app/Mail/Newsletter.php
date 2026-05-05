<?php

namespace App\Mail;

use App\Models\Contributor;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class Newsletter extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $newsletter;
    private $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($newsletter, $user)
    {
        $this->newsletter = $newsletter;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Log::channel('info')->info('Sent From: ' . config('mail.host'));
        $html = str_replace('{name}', $this->user->name, $this->newsletter->html);
        if (get_class($this->user) == User::class)
            $html = str_replace('{unsubscribe}', route('user.profile'), $html);
        elseif (get_class($this->user) == Contributor::class)
            $html = str_replace('{unsubscribe}', 'https://contributor.arabsstock.com/ar/account', $html);
        return $this->from(config('newsletter.mail.from.address'), config('newsletter.mail.from.name'))->subject($this->newsletter->subject)->html($html);
    }
}
