<?php

namespace App\Export;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DownloadExport implements WithMapping, WithHeadings, FromCollection, ShouldAutoSize
{
    protected $downloads;

    /**
     * @param $downloads
     */
    public function __construct($downloads)
    {
        $this->Objects = $downloads;
    }


    /**
     * @param mixed $download
     * @return array
     */
    public function map($download): array
    {
        return [
            $download->id,
            class_basename($download->entity_type),
            $download->entity_id,
            ($download->user) ? $download->user->name : '-',
            @$download->entity->user->name,
            $download->ip,
            $download->purchase ? "{$download->purchase->profit_ratio}%" : '-',
            $download->additional_credits ?: 0,
            $download->additional_credits_reason ? __("plans.additional_credits_reasons.{$download->additional_credits_reason}") : '-',
            number_format($download->purchase?($download->unit_price-$download->purchase->profit_value):$download->unit_price,2),
            $download->purchase ? $download->purchase->profit_value : '-',
            $download->unit_price,
            $download->created_at,
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
        array_push($heading, 'Download ID');
        array_push($heading, __('global.entity_type'));
        array_push($heading, __('global.entity_id'));
        array_push($heading, __('global.user-plans.fields.user'));
        array_push($heading, 'رُفع بواسطة');
        array_push($heading, __('global.ip'));
        array_push($heading, __('global.profit_ratio'));
        array_push($heading, __('global.additional_credits'));
        array_push($heading, __('global.additional_credits_reason'));
        array_push($heading, __('global.arabsstock_profit_value'));
        array_push($heading, __('global.profit_value'));
        array_push($heading, __('global.unit_price'));
        array_push($heading, __('global.downloaded_at'));
        return $heading;
    }
}
