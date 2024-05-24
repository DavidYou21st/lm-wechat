<?php

namespace App\Console;

use App\Console\Commands\ConsoleMakeCommand;
use App\Console\Commands\ControllerMakeCommand;
use App\Console\Commands\MiddlewareMakeCommand;
use App\Console\Commands\ModelMakeCommand;
use App\Console\Commands\RepositoryMakeCommand;
use App\Console\Commands\RequestMakeCommand;
use App\Jobs\GetQidianCustomerBaseInfoJob;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use App\Jobs\GetQidianCustomerListJob;

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
        //生成Request命令
        RequestMakeCommand::class,
        //生成Console命令
        ConsoleMakeCommand::class,
        //生成Repository命令
        RepositoryMakeCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 拉取企点客户列表
//        $schedule->call(new GetQidianCustomerListJob)->withoutOverlapping()->everyMinute(); // 每小时运行一次
//
//        // 拉取企点客户信息
//        $schedule->job(new GetQidianCustomerBaseInfoJob)->daily(); // 每天运行一次

        // 你可以定义多个任务
        // $schedule->command('emails:send')->dailyAt('15:00'); // 每天下午3点运行
    }
}
