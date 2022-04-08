<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Ticket;
use App\Helpers\CommonHelper;
use App\Events\SendNotification;

class NotAssignTicketNotificationCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'not-assign-ticket-notification:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'New generated ticket isn’t assigned to the staff in 24 hours';

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
     * Send the notification to super admin when ticket is not assign to any admin since last 24 hrs.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $tickets = Ticket::select('id', 'user_id', 'ticket_id', 'created_at')
                ->whereDate('created_at', '=', Carbon::parse('-24 hours')->toDateString())
                ->where('user_id', null)
                ->whereNotIn('status', [config('constants.TICKET.COMPLETE'), config('constants.TICKET.CANCEL')])
                ->get();

            if ($tickets->count() > 0) {
                foreach ($tickets as $key => $value) {
                    $notificationData = [
                        // 'sender_id' => $trainer->id,
                        'receiver_id' => config('constants.ADMIN_ROLE.SUPER_ADMIN'),
                        // 'sender_type' => config('constants.NOTIFICATION_TYPE.TRAINER'),
                        'receiver_type' => config('constants.NOTIFICATION_TYPE.USER'),
                        'title' => 'Unassign ticket',
                        'message' => 'Ticket id ' . $value->ticket_id . ' is not assign to any admin',
                        'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.TICKET'),
                    ];
                    event(new SendNotification($value, $notificationData));
                }
            }
        } catch (\Exception $ex) {
            CommonHelper::sendCronExceptionMail('NotAssignTicketNotificationCron', $ex->getMessage(), $ex->getLine());
        }
    }
}
