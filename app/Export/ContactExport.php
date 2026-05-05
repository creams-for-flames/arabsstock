<?php

namespace App\Export;

use App\Models\Contact;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ContactExport implements WithMapping, WithHeadings, FromCollection, WithColumnWidths
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
        /**@var $object Contact */
        $arr['image'] = optional(optional($object->images->first)->image)->getOriginal('image');
        $arr['image'] = $arr['image'] && file_exists(public_path($arr['image'])) ? (url($arr['image']) . '') : '';
        $arr['name'] = @$object->name;
        $arr['country'] = @$object->country->name_ar;
        $arr['city'] = optional($object->getRelation('city'))->name_ar;
        $arr['age'] = $object->age;
        $arr['date'] = $object->created_at ? Carbon::parse($object->created_at)->format('Y-m-d') : '#';
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
        array_push($heading, __('global.image'));
        array_push($heading, __('users.name'));
        array_push($heading, __('global.country'));
        array_push($heading, __('global.city'));
        array_push($heading, __('global.age'));
        array_push($heading, __('global.app_date'));
        return $heading;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 100,
            'B' => 55,
            'C' => 45,
        ];
    }

}
