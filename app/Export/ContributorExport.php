<?php

namespace App\Export;

use App\Models\ContributorImage;
use App\Models\ContributorVector;
use App\Models\ContributorVideo;
use App\Models\Purchase;
use App\Models\Withdraw;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ContributorExport implements WithMapping, WithHeadings, FromCollection, ShouldAutoSize
{
    protected $Objects;
    protected $Request;

    public function __construct($Objects, $Request)
    {
        $this->Objects = $Objects;
        $this->Request = $Request;
    }


    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        $arr['id'] = $row->id;
        $arr['name'] = $row->name;
        $Images = ContributorImage::where('contributor_id',$row->id);
        $Vectors = ContributorVector::where('contributor_id',$row->id);
        $Videos = ContributorVideo::where('contributor_id',$row->id);
        $Downloads = Purchase::where('contributor_id',$row->id);
        $Withdraws = Withdraw::where('contributor_id',$row->id);
        if($this->Request->filled('date_from') && $this->Request->filled('date_to')){
            $Images = $Images->whereBetween('created_at',[Carbon::parse($this->Request->date_from),Carbon::parse($this->Request->date_to)]);
            $Vectors = $Vectors->whereBetween('created_at',[Carbon::parse($this->Request->date_from),Carbon::parse($this->Request->date_to)]);
            $Videos = $Videos->whereBetween('created_at',[Carbon::parse($this->Request->date_from),Carbon::parse($this->Request->date_to)]);
            $Downloads = $Downloads->whereBetween('created_at',[Carbon::parse($this->Request->date_from),Carbon::parse($this->Request->date_to)]);
            $Withdraws = $Withdraws->whereBetween('created_at',[Carbon::parse($this->Request->date_from),Carbon::parse($this->Request->date_to)]);
        }
        if($this->Request->filled('date_from') && !$this->Request->filled('date_to')){
            $Images = $Images->where('created_at','>',Carbon::parse($this->Request->date_from));
            $Vectors = $Vectors->where('created_at','>',Carbon::parse($this->Request->date_from));
            $Videos = $Videos->where('created_at','>',Carbon::parse($this->Request->date_from));
            $Downloads = $Downloads->where('created_at','>',Carbon::parse($this->Request->date_from));
            $Withdraws = $Withdraws->where('created_at','>',Carbon::parse($this->Request->date_from));
        }
        if(!$this->Request->filled('date_from') && $this->Request->filled('date_to')){
            $Images = $Images->where('created_at','<',Carbon::parse($this->Request->date_to));
            $Vectors = $Vectors->where('created_at','<',Carbon::parse($this->Request->date_to));
            $Videos = $Videos->where('created_at','<',Carbon::parse($this->Request->date_to));
            $Downloads = $Downloads->where('created_at','<',Carbon::parse($this->Request->date_to));
            $Withdraws = $Withdraws->where('created_at','<',Carbon::parse($this->Request->date_to));
        }
        $Images=$Images->count();
        $Vectors=$Vectors->count();
        $Videos=$Videos->count();
        $arr['images_count'] = $Images.'';
        $arr['vectors_count']=$Vectors.'';
        $arr['videos_count']=$Videos.'';
        $arr['content_count'] = ($Images+$Vectors+$Videos).'';
        $arr['downloads_count']=(clone($Downloads))->count() .'';
        $arr['profits_count']=(clone($Downloads))->sum('profit_value') .'';
        $arr['withdrawal_amount']=(clone($Withdraws))->where('status_payout',1)->sum('value_withdraw').'';
        $arr['pending_withdrawal_amount']=(clone($Withdraws))->where('status_payout',0)->sum('value_withdraw').'';
        return $arr;
    }

    /**
     */
    public function collection()
    {
        return $this->Objects;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        $heading = array();
        array_push($heading,'#');
        array_push($heading,__('global.user-plans.fields.contributor'));
        array_push($heading,__('global.images_count'));
        array_push($heading,__('global.vectors_count'));
        array_push($heading,__('global.videos_count'));
        array_push($heading,__('global.content_count'));
        array_push($heading,__('global.downloads_count'));
        array_push($heading,__('global.profits_count'));
        array_push($heading,__('global.withdrawal_amount'));
        array_push($heading,__('global.pending_withdrawal_amount'));
        return $heading;
    }
}
