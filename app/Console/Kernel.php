<?php

namespace App\Console;

use App\Console\Commands\Statistics\Update;
use App\Console\Commands\XML\CategoryXml;
use App\Console\Commands\XML\ImagesXml;
use App\Console\Commands\XML\MasterXml;
use App\Console\Commands\XML\StaticPagesXml;
use App\Console\Commands\XML\TagImagesXml;
use App\Console\Commands\XML\VideosXml;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\SyncBraintreePlans::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('telescope:clear')->dailyAt('01:00');
        $schedule->command('subscriptions:delete-scheduled')->daily();
        $schedule->command('subscriptions:renew')->daily();
        $schedule->command('xml:categories')->dailyAt('02:2');
        $schedule->command('xml:images')->dailyAt('2:30');
        $schedule->command('xml:videos')->dailyAt('2:30');
        $schedule->command('xml:vectors')->dailyAt('2:30');
        $schedule->command('xml:staticpages')->dailyAt('02:8');
        $schedule->command('xml:tagimages')->dailyAt('02:9');
        $schedule->command('xml:tagvideos')->dailyAt('02:10');
        $schedule->command('xml:tagvectors')->dailyAt('02:11');
        $schedule->command('elasticsearch:reindex')->hourlyAt(15);
        $schedule->command('mail:send_daily_contributor_sales')->dailyAt('23:59');
        $schedule->command('statistics:update')->cron('0 */6 * * *');
        $schedule->command('statistics:payments')->cron('0 */3 * * *');
        $schedule->call(function(){
            Update::notifications();
        })->everyFiveMinutes();
        // $schedule->command('mail:send_weekly_letter')->everyFiveMinutes();
        $schedule->command('xml:master')->dailyAt(4);
        $schedule->command('offensive-word:seed')->daily();
        $schedule->call(function () {
            $data = \DB::select("
            select image_id, tag from ( SELECT image_id, tag, count(*) as c FROM `image_tags` GROUP by image_id, tag HAVING c > 1  ) as t1
            ");

            foreach ($data as $item) {
                $first_id = \DB::table('image_tags')
                    ->where('image_id', $item->image_id)
                    ->where('tag', $item->tag)
                    ->first()->id;

                \DB::table('image_tags')
                    ->where('id', $first_id)
                    ->delete();
            }
        })->dailyAt('04:03');
        $schedule->command('bot-sessions:clear')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
