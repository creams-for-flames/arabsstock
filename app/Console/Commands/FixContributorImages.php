<?php

namespace App\Console\Commands;

use App\Jobs\HashImagesJob;
use Illuminate\Console\Command;
use App\Jobs\FixContributorImagesJob;

class FixContributorImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'do:contributor_images_preview';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'contributor images add preview';

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
        dispatch(new FixContributorImagesJob());

    }
}
