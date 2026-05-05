<?php

namespace App\Jobs;

use App\Contexts\PayPal;
use App\Contexts\Plans;
use App\Models\Plan;
use App\Models\Promocode;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class CreatePromocodePaypalPlans implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $promocode;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($promocode)
    {
        $this->promocode = $promocode;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->promocode->plans()->whereNotNull('plans.paypal_plan')->whereNull('plan_promocode.paypal_plan')->get() as $plan) {
            $this->create_plan($this->promocode, $plan);
        }
    }

    private function create_plan(Promocode $promocode, Plan $plan)
    {
        Plans::create_paypal_product_for_flex();
        $billing_cycles = [
            [
                "frequency" => [
                    "interval_unit" => 'MONTH',
                    "interval_count" => 1,
                ],
                "tenure_type" => "TRIAL",
                "sequence" => 1,
                "total_cycles" => 1,
                "pricing_scheme" => [
                    "fixed_price" => [
                        "value" => $promocode->calculate_price($plan->price),
                        "currency_code" => "USD",
                    ],
                ],
            ],
            [
                "frequency" => [
                    "interval_unit" => strtoupper($plan->frequency),
                    "interval_count" => 1,
                ],
                "tenure_type" => "REGULAR",
                "sequence" => 2,
                "total_cycles" => 0,
                "pricing_scheme" => [
                    "fixed_price" => [
                        "value" => $plan->price,
                        "currency_code" => "USD",
                    ],
                ],
            ],
        ];
        $params = [
            'product_id' => sprintf('%06d', 4),
            'name' => "{$plan->title_en} ({$promocode->code})",
            'description' => "#Arabsstock{$plan->id} with promocode ({$promocode->code})",
            "billing_cycles" => $billing_cycles,
            "payment_preferences" => [
                "auto_bill_outstanding" => false,
                "setup_fee" => [
                    "value" => 0,
                    "currency_code" => "USD",
                ],
                "setup_fee_failure_action" => "CONTINUE",
                "payment_failure_threshold" => 3,
            ],
        ];
        Log::channel('info')->info('Create paypal plan', $params);
        $response = \App\Contexts\PayPal::create_plan($params);
        if ($response->error_code !== 0) {
            Log::error("Cant create promcode plan:{$response->error}", ['plan_id' => $plan->id, 'promocode_id' => $promocode->id]);
        }
        $paypal_plan = (array)$response->result;
        $promocode->plans()->updateExistingPivot($plan->id, ['paypal_plan' => $paypal_plan['id']]);
    }

}
