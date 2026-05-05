<?php

namespace App\Mail;
use App\Models\WeeklyLetterClient;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WeeklyLetterEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $weekly_letter;
    protected $unsubscribe_url;
    public function __construct(WeeklyLetterClient $weekly_letter,$unsubscribe_url)
    {
        $this->weekly_letter = $weekly_letter;
        $this->unsubscribe_url = $unsubscribe_url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = $this->weekly_letter;
        $unsubscribe_url = $this->unsubscribe_url;
        $title = $data->title??"جديدنا هذا الأسبوع | عربستوك";
        return $this->markdown('emails.weekly_letter')->subject($title)->with(['wlo'=>$data,'unsubscribe_url'=>$unsubscribe_url]);

    }
}
