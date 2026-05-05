<?php

namespace App\Console\Commands;

use App\Jobs\FixVectorsIob;
use Illuminate\Console\Command;

class FixVectors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:vectors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix Vector';

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
        dispatch(new FixVectorsIob());

    }
}
