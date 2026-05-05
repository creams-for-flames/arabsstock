<?php

namespace App\Jobs;

use App\Models\ImageSearchKey;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\OffensiveWord;
use App\Models\VectorSearchKey;
use App\Models\VideoSearchKey;
use Illuminate\Support\Facades\Log;

class OffensiveWordSeed implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $key_words = ["sexy",'fuck',"سكس"];
        $OffensiveWord = new OffensiveWord();
        $data = $this->GetDataFromModel(new ImageSearchKey(),$key_words);
        $this->InsertInOffensiveWordAndDeleteFromSearchKey($OffensiveWord,$data);
        $data = $this->GetDataFromModel(new VideoSearchKey(),$key_words);
        $this->InsertInOffensiveWordAndDeleteFromSearchKey($OffensiveWord,$data);
        $data = $this->GetDataFromModel(new VectorSearchKey(),$key_words);
        $this->InsertInOffensiveWordAndDeleteFromSearchKey($OffensiveWord,$data);

        foreach ($data as $key => $item) {
            $OffensiveWord->updateOrCreate([
                'key_word'=>$item->key_word
            ]);
        }
        
        Log::channel('info')->info("End OffensiveWordSeed");
    }

    public function GetDataFromModel($ModelName,$key_words)
    {
        $data = $ModelName->where(function($q) use($key_words){
            foreach ($key_words as $key => $key_word) {
                $q->orWhere('key_word','like',"%{$key_word}%");
            }
        })->get();
        return $data;
    }

    public function InsertInOffensiveWordAndDeleteFromSearchKey($OffensiveWord,$data)
    {
        foreach ($data as $key => $item) {
            $OffensiveWord->updateOrCreate([
                'key_word'=>$item->key_word
            ]);
            $item->delete();
        }
    }
}
