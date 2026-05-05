<?php

namespace App\Jobs;

use App\Models\BouncedEmail;
use App\Models\ContributorImage;
use App\Models\ContributorVector;
use App\Models\ContributorVideo;
use Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\EmailAlertContributorFileRejected;

class SendEmailAlertContributorFileRejected implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $contributor_file_id;
    protected $type;
    protected $review_notes;
    /*
        $contributor_file_id = $value;
        $type = 'images';
        $review_notes = $request_params['notes'];
    */
    public function __construct($contributor_file_id , $type ,$review_notes)
    {
        $this->contributor_file_id = $contributor_file_id;
        $this->type = $type;
        $this->review_notes = $review_notes;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $contributor_file = NULL;
        switch ($this->type) {
            case 'images':
                $contributor_file = ContributorImage::with(['contributor:id,email,name'])
                ->where('id', $this->contributor_file_id)->select('review_notes','contributor_id','contributor_stage','original_name')
                ->first();
                break;
            case 'videos':
                $contributor_file = ContributorVideo::with(['contributor:id,email,name'])
                ->where('id', $this->contributor_file_id)->select('review_notes','contributor_id','contributor_stage','original_name')
                ->first();
                break;
            case 'vectors':
                $contributor_file = ContributorVector::with(['contributor:id,email,name'])
                ->where('id', $this->contributor_file_id)->select('review_notes','contributor_id','contributor_stage','original_name')
                ->first();
                break;
        }
        if (!$contributor_file) {
            return 1;
        }
        
        if (isset($contributor_file)) {
            $mail = $contributor_file->contributor->email;
            $data['contributor'] = $contributor_file->contributor;
            $data['type'] = $this->type;
            $data['contributor_file'] = $contributor_file;
            if (BouncedEmail::where('email', $mail)->count())
                return;
            $email = new EmailAlertContributorFileRejected($data);
            Mail::to($mail)->send($email);
        }
    }
}
