<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\{DB, Log,Storage};
use Mpdf\Mpdf;
use Illuminate\Support\Str;

class ConvertContractToPDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $table;
    protected $folder_id;
    protected $user_id;
    public function __construct($table,$folder_id,$user_id)
    {
        $this->table = $table;
        $this->folder_id = $folder_id;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $table = $this->table;
        $folder_id = $this->folder_id;
        $user_id = $this->user_id;
        $data = DB::table($table);
        $filename = Str::random(30).'.pdf';
        Log::channel('info')->debug("ConvertContractToPDF");
        switch ($table) {
            case 'photographer_image_folder':
            case 'photographer_video_folder':
                $query = $data->where('photographer_id',$user_id)->where('folder_id',$folder_id)->first();
                break;
            case 'actor_image_folder':
            case 'actor_video_folder':
                $query = $data->where('photographer_id',$user_id)->where('folder_id',$folder_id)->first();
                break; 
            case 'location_image_folder':
            case 'location_video_folder':
                $query = $data->where('session_location_id',$user_id)->where('folder_id',$folder_id)->first();
                break;            

        }
        $table_without_folder = str_replace('_folder','',$table);
        $file_path =  "uploads/sessions/{$folder_id}/{$query->id}/{$table_without_folder}/{$user_id}/{$filename}";
        $status = $this->convertToPdf($query,$file_path);
        if ($status) {
            if (Storage::disk('s3')->exists($query->contract_file)) {
                Storage::disk('s3')->delete($query->contract_file);
            }
           $update = $data->where('id',$query->id)->update(['contract_file' => $file_path,'is_uploaded'=>1]);
        }
    }

    
    public function convertToPdf($file, $filepath)
    {
        $html = $file->contract;
    
        // Define a temporary directory where Mpdf can write temporary files
        $tempDir = storage_path('app/mpdf_temp');
    
        // Create the temporary directory if it doesn't exist
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }
    
        $document = new Mpdf([
            'format' => 'A4',
            'tempDir' => $tempDir, // Specify the temporary directory
        ]);
    
        $document->WriteHTML($html);
        $status_file = Storage::disk('s3')->put($filepath, $document->Output(basename($filepath), "S"));
    
        return $status_file;
    }
    
}
