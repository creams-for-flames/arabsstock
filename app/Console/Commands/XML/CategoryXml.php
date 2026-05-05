<?php

namespace App\Console\Commands\XML;

use App\Jobs\XML\XmlCategoryCreator;
use Illuminate\Console\Command;

class CategoryXml extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xml:categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Xml File For Category Links';

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
        dispatch(new XmlCategoryCreator());
    }
}
