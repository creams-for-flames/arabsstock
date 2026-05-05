<?php

namespace App\Export;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ContributorDownloadExport implements WithMapping, WithHeadings, FromCollection, WithColumnFormatting, WithStyles, ShouldAutoSize
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
//            $object->id,
            class_basename($object->purchaseable_type),
            $object->purchaseable_id,
            @$object->user->name ?: $object->user_id,
            $object->contributor->name,
            intval($object->plan_price) ?: 'M',
            $object->unit_price,
            $object->profit_ratio,
            $object->profit_value,
            $object->download ? ($object->download->additional_credits ?: 0) : 0,
            $object->download?($object->download->additional_credits_reason ? __("plans.additional_credits_reasons.{$object->download->additional_credits_reason}") : '-'):'-',
            $object->download?(number_format($object->download->purchase ? ($object->download->unit_price - $object->download->purchase->profit_value) : $object->download->unit_price, 2)):'0',
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
//        array_push($heading, '#');
        array_push($heading, __('global.entity_type'));
        array_push($heading, __('global.entity_id'));
        array_push($heading, __('global.user-plans.fields.user'));
        array_push($heading, __('global.user-plans.fields.contributor'));
        array_push($heading, __('global.plan_price'));
        array_push($heading, __('global.unit_price'));
        array_push($heading, __('global.profit_ratio'));
        array_push($heading, __('global.profit_value'));
        array_push($heading, __('global.additional_credits'));
        array_push($heading, __('global.additional_credits_reason'));
        array_push($heading, __('global.arabsstock_profit_value'));
        array_push($heading, __('global.downloaded_at'));
        return $heading;
    }

    public function columnFormats(): array
    {
        return [
//            'E' => \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'E' => ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,]],
        ];
    }
}
