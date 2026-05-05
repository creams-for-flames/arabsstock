<?php

namespace App\Console\Commands;

use App\Jobs\HashImagesJob;
use Illuminate\Console\Command;

class HashImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hash:images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hash Image';

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
        dispatch(new HashImagesJob());

    }
}
