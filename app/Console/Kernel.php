<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\WorkingHourTicketNotificationCron;
use App\Console\Commands\NotAssignTicketNotificationCron;
use App\Console\Commands\SubscribeChat;
use App\Console\Commands\ChatNotReplyCustomer;
use App\Console\Commands\ChatNotReplyTrainer;
use App\Console\Commands\GlobalRequestTicket;
use App\Console\Commands\NewTicket;
use App\Console\Commands\TrainerNotVerified;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        WorkingHourTicketNotificationCron::class,
        NotAssignTicketNotificationCron::class,
        SubscribeChat::class,
        ChatNotReplyCustomer::class,
        ChatNotReplyTrainer::class,
        GlobalRequestTicket::class,
        NewTicket::class,
        TrainerNotVerified::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('ticket:working-hourse-ticket-not-assign:cron')
            ->everyThirtyMinutes();
        $schedule->command('not-assign-ticket-notification:cron')
            ->daily();
        // $schedule->command('subscribe:chat')
        //     ->everyMinute()
        //     ->withoutOverlapping();
        $schedule->command('chat:not-reply-customer')
            ->everyFifteenMinutes();
        $schedule->command('chat:not-reply-trainer')
            ->everyFifteenMinutes();
        $schedule->command('ticket:global-request')
            ->everyTenMinutes();
        $schedule->command('new:ticket')
            ->everyFiveMinutes();
        $schedule->command('trainer:not-verified')
            ->daily();
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
