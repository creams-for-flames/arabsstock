<?php

namespace App\Export;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WithdrawExport implements WithMapping, WithHeadings, FromCollection, ShouldAutoSize
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
        return [
            $object->id,
            ($object->contributor) ? $object->contributor->name : '-',
            $object->email,
            $object->value_withdraw,
            $object->fees,
            $object->status_desc_payout,
            $object->images_purchases ?: 0,
            $object->videos_purchases ?: 0,
            $object->vectors_purchases ?: 0,
            $object->created_at,
        ];
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
        array_push($heading, 'Withdraw ID');
        array_push($heading, __('global.user-plans.fields.contributor'));
        array_push($heading, __('global.E-mail'));
        array_push($heading, __('views.withdraw_value'));
        array_push($heading, __('views.withdraw_fees'));
        array_push($heading, __('views.Status'));
        array_push($heading, __('Images'));
        array_push($heading, __('Videos'));
        array_push($heading, __('Vectors'));
        array_push($heading, __('global.app_created_at'));
        return $heading;
    }
}
