<?php

namespace App\Console;

use App\Console\Commands\ControllerMakeCommand;
use App\Console\Commands\MiddlewareMakeCommand;
use App\Console\Commands\ModelMakeCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //生成控制器命令
        ControllerMakeCommand::class,
        //生成中间件命令
        MiddlewareMakeCommand::class,
        //生成Model命令
        ModelMakeCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
