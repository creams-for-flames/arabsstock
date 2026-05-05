<?php

namespace App\Console\Commands;

use App\Jobs\HashVideosJob;
use Illuminate\Console\Command;

class HashVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hash:videos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hash videos';

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
        dispatch(new HashVideosJob());

    }
}
