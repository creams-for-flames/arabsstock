<?php

namespace App\Console\Commands\XML;

use App\Jobs\XML\XmlMasterCreator;
use Illuminate\Console\Command;

class MasterXml extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xml:master';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Xml Index For Xml Files';

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
        dispatch(new XmlMasterCreator());
    }
}
