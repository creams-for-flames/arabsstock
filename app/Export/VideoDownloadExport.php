<?php

namespace App\Export;

use App\Models\Purchase;
use App\Models\Video;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VideoDownloadExport implements WithMapping, WithHeadings, FromCollection, ShouldAutoSize
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
        $purchase = Purchase::where('purchaseable_type', Video::class)->where('purchaseable_id', $object->video_id)->where('user_id', $object->user_id)->first();
        return [
            $object->id,
            $object->video_id,
            $object->client ? $object->client->name : '-',
            $object->ip,
            $object->plan->title_en ?: '-',
            $object->subscription ? "{$object->subscription->amount}$" : '',
            $purchase ? $purchase->contributor->name : '-',
            $purchase ? $purchase->profit_ratio : '',
            $purchase ? $purchase->profit_value : '',
            $object->subscription ? round(($object->subscription->amount / $object->plan->downloads_count), 2) : '0',
            $object->subscription ? $object->subscription->plan_type : '-',
            $object->date,
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
        array_push($heading, __('global.video'));
        array_push($heading, __('global.user-plans.fields.user'));
        array_push($heading, __('global.ip'));
        array_push($heading, __('global.plan'));
        array_push($heading, __('global.plan_price'));
        array_push($heading, __('global.user-plans.fields.contributor'));
        array_push($heading, __('global.profit_ratio'));
        array_push($heading, __('global.profit_value'));
        array_push($heading, __('global.unit_price'));
        array_push($heading, __('global.plan_type'));
        array_push($heading, __('global.downloaded_at'));
        return $heading;
    }
}
