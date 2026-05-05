<?php

namespace App\Console\Commands\XML;

use App\Jobs\XML\XmlTagImagesCreator;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TagImagesXml extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xml:tagimages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Xml File For Image Tags Links';

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
        Storage::disk('public')->makeDirectory('feeds');
        $count = Tag::whereHas('images')->groupBy('slug')->get()->count();
        $per_page = 20000;
        $pages = ceil($count / $per_page);
        for ($sitemapCounter = 1; $sitemapCounter <= $pages; $sitemapCounter++) {
            $from = ($sitemapCounter - 1) * $per_page;
            dispatch(new XmlTagImagesCreator($sitemapCounter, $from, $per_page));
        }
    }
}
