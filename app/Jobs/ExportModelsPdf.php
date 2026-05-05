<?php

namespace App\Jobs;

use App\Mail\SendCastPdfLink;
use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ExportModelsPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->onQueue('heavy');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        if (!file_exists(public_path('uploads/cast')))
            mkdir(public_path('uploads/cast'), 0777, true);
        $time = time();
        $file_name = "uploads/cast/cast_{$time}.pdf";
        $pdf_path = public_path($file_name);
        $html_path = public_path("uploads/cast/cast_{$time}.html");
        $results = Contact::with('city', 'images')->selectRaw('contacts.*,cities.sort')
            ->join('cities', 'cities.id', '=', 'contacts.city')
            ->orderBy('cities.sort')
            ->orderBy('birth_date')
            ->get();
        Log::channel('info')->info("Start export cast");

        file_put_contents($html_path, view('admin_v2_super.contact.export', compact('results'))->render());
        $command = "wkhtmltopdf {$html_path} {$pdf_path} &";
        exec($command);
        Log::channel('info')->info("Finish export cast");
        dispatch(function () use ($file_name, $pdf_path, $html_path) {
            Storage::disk('s3')->put($file_name, file_get_contents($pdf_path));
            unlink($pdf_path);
            unlink($html_path);
        });
        $emails = explode(',', env('CAST_EXPORT_EMAILS'));
        Mail::to($emails)->send(new SendCastPdfLink($file_name));
    }
}
