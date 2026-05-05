<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Braintree_Plan;
use App\Models\ImagePlan;

class SyncBraintreePlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'braintree:sync-plans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync with online plans on Braintree';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
  public function handle()
    {
        // Empty table
       // ImagePlan::truncate();

        // Get plans from Braintree
        $braintreePlans = Braintree_Plan::all();

        // uncomment the line below to dump the plans when running the command
        // var_dump($braintreePlans);

        // Iterate through the plans while populating our table with the plan data
        foreach ($braintreePlans as $braintreePlan) {
            $plan = ImagePlan::withTrashed()->where(['uuid'=>$braintreePlan->id])->first();
            if(!$plan)
            $plan = ImagePlan::create([
                'title_ar' => $braintreePlan->name,
                'title_en' => $braintreePlan->name,
                'slug' => \Illuminate\Support\Str::slug($braintreePlan->name),
                'braintree_plan' => $braintreePlan->id,
                'uuid' => $braintreePlan->id,
                'price' => $braintreePlan->price,
                // 'description' => $braintreePlan->description,
                'downloads_count' => 0,
                'type' => 'monthly',
                'status' => false,
                'created_at' => now()
            ]);

           else{
           $plan->update([
            'price'      => $braintreePlan->price,
            'title_ar'   => $braintreePlan->name,
            'title_en'   => $braintreePlan->name,
            'updated_at' => now()]);
          }

        }
    }
}
