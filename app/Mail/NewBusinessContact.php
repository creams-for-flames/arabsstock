<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewBusinessContact extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $message;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to([config('mail.info_email')])
            ->subject('#' . $this->message->id . ' رسالة جديدة - للأعمال ')
            ->from('noreply@arabsstock.com')
            ->markdown('emails.contact.business')
            ->with(['message' => $this->message]);
    }
}
