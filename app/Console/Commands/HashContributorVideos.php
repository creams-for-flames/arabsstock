<?php

namespace App\Console\Commands;

use App\Jobs\HashContributorVideosJob;
use Illuminate\Console\Command;

class HashContributorVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hash:contributor_videos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hash Contributor Video';

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
        dispatch(new HashContributorVideosJob());
    }
}
