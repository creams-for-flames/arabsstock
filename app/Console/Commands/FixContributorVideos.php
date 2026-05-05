<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\FixContributorVideosJob;

class FixContributorVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'do:contributor_videos_preview';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'do contributor videos preview';

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
        dispatch(new FixContributorVideosJob());

    }
}
