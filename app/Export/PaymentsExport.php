<?php

namespace App\Export;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaymentsExport implements WithMapping, WithHeadings, FromCollection, ShouldAutoSize
{
    protected $subscriptions;

    /**
     * @param $subscriptions
     */
    public function __construct($subscriptions)
    {
        $this->Objects = $subscriptions;
    }


    /**
     * @param mixed $subscription
     * @return array
     */
    public function map($subscription): array
    {
        return [
            $subscription->id,
            $subscription->payment_id ?: $subscription->subscription_id,
            $subscription->plan ? $subscription->plan->title : '-',
            $subscription->plan ? $subscription->plan->credits_count : '-',
            $subscription->remaining_credits,
            $subscription->amount,
            $subscription->user ? $subscription->user->name : '-',
            $subscription->country ? $subscription->country->name_ar : '' . ' - ' . @$subscription->city->name_ar,
            $subscription->created_at,
            $subscription->payment_method->title_en,
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
        array_push($heading, 'Subscription ID');
        array_push($heading, __('Invoice Number'));
        array_push($heading, __('global.plan'));
        array_push($heading, __('credits'));
        array_push($heading, __('global.user-plans.fields.remaining-credits'));
        array_push($heading, __('admin.price'));
        array_push($heading, __('global.user-plans.fields.user'));
        array_push($heading, __('global.user-plans.fields.place'));
        array_push($heading, __('admin.date'));
        array_push($heading, __('Payment Method'));
        return $heading;
    }
}
