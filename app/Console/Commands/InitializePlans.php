<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InitializePlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plans:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        \App\Contexts\Plans::initialize_plans_images();
        \App\Contexts\Plans::initialize_plans_videos();
        \App\Contexts\Plans::initialize_plans_vectors();
        \App\Contexts\Plans::initialize_plans_flex();
        \App\Contexts\Stripe::initialize_plans();
        $this->info('Plans Initialized successfully.');
    }
}
