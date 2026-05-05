<?php

namespace App\Export;

use App\Models\Purchase;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VideoExport implements WithMapping,WithHeadings,FromCollection
{
    protected $Objects;

    /**
     * @param $Objects
     */
    public function __construct($Objects)
    {
        $this->Objects = $Objects;
    }


    /**
     * @param mixed $object
     * @return array
     */
    public function map($object): array
    {
        $arr['id'] = $object->id;
        $arr['up_type'] = ($object->contributor_video_id == 0)?'ArStock':'Contributor';
        $arr['Contributor'] = ($object->contributor_video_id != 0 &&$object->user_id != 1 && $object->user)?$object->user->name:'-';
        $arr['title'] = $object->title_en;
        $arr['time'] = Carbon::parse($object->date)->format('H:i A');
        $arr['date'] = Carbon::parse($object->date)->format('Y-m-d');
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
        array_push($heading,'Video ID');
        array_push($heading,__('global.upload'));
        array_push($heading,__('global.contributor'));
        array_push($heading,__('global.title'));
        array_push($heading,__('global.app_time'));
        array_push($heading,__('global.app_date'));
        return $heading;
    }
}
