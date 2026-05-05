<?php

namespace App\Console\Commands\XML;

use App\Jobs\XML\XmlImagesCreator;
use Illuminate\Console\Command;

class ImagesXml extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xml:images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Xml File For Image Links';

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
        dispatch(new XmlImagesCreator());


    }
}
