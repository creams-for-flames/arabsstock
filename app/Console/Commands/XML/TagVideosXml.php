<?php

namespace App\Console\Commands\XML;

use App\Jobs\XML\XmlTagVideosCreator;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TagVideosXml extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xml:tagvideos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Xml File For Video Tags Links';

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
        $count = Tag::whereHas('videos')->groupBy('slug')->get()->count();
        $per_page = 20000;
        $pages = ceil($count / $per_page);
        for ($sitemapCounter = 1; $sitemapCounter <= $pages; $sitemapCounter++) {
            $from = ($sitemapCounter - 1) * $per_page;
            dispatch(new XmlTagVideosCreator($sitemapCounter, $from, $per_page));
        }
    }
}
