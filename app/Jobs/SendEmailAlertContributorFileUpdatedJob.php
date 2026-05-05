<?php

namespace App\Jobs;

use App\Mail\SendEmailAlertContributorFileUpdated;
use App\Models\BouncedEmail;
use App\Models\ContributorImage;
use App\Models\ContributorVector;
use App\Models\ContributorVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendEmailAlertContributorFileUpdatedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $id;
    protected $type;
    public function __construct($id,$type)
    {
        $this->id = $id;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $type = $this->type;
        $data = NULL;
        $id = $this->id;
        switch ($type) {
            case 'ContributorImage':
                $data = ContributorImage::with('contributor')->findOrFail($id);
                break;
            case 'ContributorVideo':
                $data = ContributorVideo::with('contributor')->findOrFail($id);

                break;
            case 'ContributorVector':
                $data = ContributorVector::with('contributor')->findOrFail($id);
                break;            

        }
        $mail = $data->contributor->email;
        if (BouncedEmail::where('email', $mail)->count())
            return;
        $email = new SendEmailAlertContributorFileUpdated($data);
        Mail::to($mail)->send($email);
    }
}
