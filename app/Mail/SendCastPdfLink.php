<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCastPdfLink extends Mailable
{
    use Queueable, SerializesModels;

    private $file_name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($file_name)
    {
        $this->file_name = $file_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $link = config('filesystems.disks.s3.url') . "/$this->file_name";
        return $this->markdown('emails.cast_exported', ['link' => $link])->subject('قائمة الممثلين في عربستوك')->from("noreply@arabsstock.com", "Arabsstock");
    }
}
