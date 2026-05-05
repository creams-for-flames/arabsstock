<?php

namespace App\Console\Commands\XML;

use App\Jobs\XML\XmlVideosCreator;
use Illuminate\Console\Command;

class VideosXml extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xml:videos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Xml File For Video Links';

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
        dispatch(new XmlVideosCreator());

    }
}
