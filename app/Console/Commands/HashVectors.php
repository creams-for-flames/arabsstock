<?php

namespace App\Console\Commands;

use App\Jobs\HashVectorsJob;
use Illuminate\Console\Command;

class HashVectors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hash:vectors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hash Vector';

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
        dispatch(new HashVectorsJob());

    }
}
