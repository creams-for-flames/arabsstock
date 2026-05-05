<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateJsLangFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-js-lang';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        foreach (['ar', 'en'] as $lang) {
            app()->setLocale($lang);
            $response = [];
            foreach (glob(resource_path() . "/lang/{$lang}/*.php") as $f) {
                $key = pathinfo($f, PATHINFO_FILENAME);
                $response[$key] = __($key);
            }
            file_put_contents(public_path("js/lang.$lang.js"), 'lang=' . json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ';');
        }
    }
}
