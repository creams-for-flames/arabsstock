<?php

namespace App\Jobs;

use App\Helper;
use App\Models\Image;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Output\Output;

class RemoveBgImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $removebg_type = "free";
    protected $id;
    public function __construct($id,$removebg_type)
    {
        $this->id = $id;
        $this->removebg_type = $removebg_type;
        $this->onQueue('image');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $id = $this->id;
        Log::channel('info')->info("RemoveBgImage Start : {$id}");
         $file = Image::findOrFail($id);
         $extension ="png";
        $image = strtolower(time() . Str::random(5) . '.' . $extension);
        $dire_images = DS . 'uploads' . DS . 'images' .DS. $file->id;
        $new_folder = $dire_images. DS ."removebg";
        $full_path = $new_folder.DS.$image;
        if($file->removebg_image === NULL || $file->removebg_image === '')
        $file->removebg_image = $full_path;
        
        $file->removebg_status = "processing";
        $file->save();

        if (Storage::disk('s3')->exists($file->large)) {
            try {
                Storage::disk('public')->put($file->large, Storage::disk('s3')->get($file->large));
                $this->CallApiRemoveBg(public_path($file->large),$file->removebg_image);
                if (Storage::disk('public')->exists($file->large)) {
                    $width = Helper::getWidth(public_path($file->removebg_image));
                    $height = Helper::getHeight(public_path($file->removebg_image));
                    $this->DeleteDir($dire_images);
                    Log::channel('info')->info("RemoveBgImage png  {$width}x{$height}");
                }
            } catch (\Throwable $th) {
                if (Storage::disk('public')->exists($file->large)) {
                    $this->DeleteDir($dire_images);
                }
                throw $th;
            }



            $file->height_removebg = $height;
            $file->width_removebg = $width;
            $file->removebg_type = $this->removebg_type;
            $file->removebg_status = "done";
            $file->removebg_created_at =  date('Y-m-d H:i:s');
            $file->save();

        }else{
            Log::channel('info')->info("RemoveBgImage file not found : {$file->large}");
        }

        Log::channel('info')->info("RemoveBgImage End : {$id}");



    }

    public function CallApiRemoveBg($input_file, $output_file)
    {
        $config =config('services.removebg');
        $type = $this->removebg_type;
        $setting = $config[$type];
        $headers = ['X-Api-Key' => $setting['api_key']];

        $client = new Client();
        $options = [
            'multipart' => [
            [
            'name' => 'image_file',
            'contents' => fopen($input_file, 'r'),
            'filename' => $input_file,
            ]
            ]]; 

        $request = new Request('POST',$setting['endpoint'], $headers);
        $response = $client->sendAsync($request, $options)->wait();
        if ($response->getStatusCode() === 200) {
            $stream = \GuzzleHttp\Psr7\Utils::streamFor($response->getBody()); 
            $data = $stream->getContents(); 
            Storage::disk('public')->put($output_file, $data);
            Storage::disk('s3')->put($output_file, $data);
            Log::channel('info')->info("RemoveBgImage output  {$output_file}");
            
        }

    }
    
    public function DeleteDir($dir)
    {
        Storage::disk('public')->deleteDirectory($dir);
    }
}
