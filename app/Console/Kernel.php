<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\Update;
use App\Jobs\UpdateOmni;
use App\Jobs\CleanListings;
use App\Jobs\SendNotification;
use App\Jobs\LogImpression;
use App\Jobs\UpdateListings;
use App\Jobs\UpdatePhotos;
use App\Jobs\UpdateAgents;
use App\Jobs\UpdateOffices;
use App\Jobs\UploadNewPhotos;
use App\Jobs\UpdateOpenHouses;
use App\Jobs\CheckPhotoConsistency;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new UpdateListings,'updaters')->hourly()->withOutOverlapping();
        $schedule->job(new UpdateOmni,'updaters')->hourly()->withOutOverlapping();
        $schedule->job(new UpdateOffices,'updaters')->hourly()->withOutOverlapping();
        $schedule->job(new UpdateOpenHouses,'updaters')->hourly()->withOutOverlapping();
        $schedule->job(new UpdateAgents,'updaters')->hourly()->withOutOverlapping();
        $schedule->job(new CleanListings,'updaters')->hourlyAt(10)->withOutOverlapping();
        $schedule->job(new UploadNewPhotos,'updaters')->hourlyAt(15)->withOutOverlapping();
        $schedule->job(new CheckPhotoConsistency,'updaters')->dailyAt('2:00')->withOutOverlapping()->timezone('America/New_York');
        $schedule->command('horizon:snapshot')->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
