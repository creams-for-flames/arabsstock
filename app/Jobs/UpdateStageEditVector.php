<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Vector;
use Illuminate\Support\Facades\Log;

class UpdateStageEditVector implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $ids;
    public function __construct(Array $ids)
    {
        $this->ids = $ids;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ids = $this->ids;
        $data = Vector::with(['category','tags'])->whereIn('id',$ids)
                    ->get();
        foreach ($data as $key => $item) {
            $stage_edit = 0;
            $last_stage_edit = $item->stage_edit;
            if(
                ( isset($item->title_ar) && $item->title_ar != "" && is_arabic($item->title_ar) ) || 
                ( isset($item->title_en) && $item->title_en != "" &&  !is_arabic($item->title_en)   ) ||
                $item->tags()->where('local','ar')->exists() ||
                $item->tags()->where('local','en')->exists() ||
                $item->category()->exists()
            )
                $stage_edit = 1;


            if(
                ( isset($item->title_ar) && $item->title_ar != "" && is_arabic($item->title_ar) ) && 
                ( isset($item->title_en) && $item->title_en != "" && !is_arabic($item->title_en)   ) &&
                $item->tags()->where('local','ar')->exists() &&
                $item->tags()->where('local','en')->exists() &&
                $item->category()->exists()
            )
                $stage_edit = 2;


                if ( $stage_edit !=  $last_stage_edit) {
                    $item->stage_edit = $stage_edit;
                    $item->save();
                    Log::channel('info')->info("UpdateStageEditVector id : {$item->id} stage edit from :{$last_stage_edit} to:  {$item->stage_edit}");
                }

        }



                    

    }
}
