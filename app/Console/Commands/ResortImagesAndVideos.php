<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResortImagesAndVideos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:resort';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

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

    \DB::update('UPDATE images SET `sort` = 0 + (20000 - 0) * RAND()');
    \DB::update('UPDATE videos SET `sort` = 0 + (20000 - 0) * RAND()');

    }
}
