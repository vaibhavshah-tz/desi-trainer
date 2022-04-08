<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Ticket;
use App\Helpers\CommonHelper;
use App\Events\SendNotification;

class WorkingHourTicketNotificationCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ticket:working-hourse-ticket-not-assign:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'New generated ticket isn’t assigned to the staff in 30 min during working hours';

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
     * Send the notification to super admin when ticket is not assign to any admin in working hourse
     * duration of notification is 30 min
     *
     * @return int
     */
    public function handle()
    {
        try {
            $currentUtcDate = Carbon::now();
            $currentIstTime = CommonHelper::convertDateToSpecificTimezone($currentUtcDate, config("constants.INTERVIEW_SUPPORT_TICKET.OFFICE_TIMEZONE"));
            $startTime = $currentIstTime->toDateString() . ' ' . config("constants.INTERVIEW_SUPPORT_TICKET.START_TIME");
            $endTime = $currentIstTime->toDateString() . ' ' . config("constants.INTERVIEW_SUPPORT_TICKET.END_TIME");

            $utcStartTime = CommonHelper::getUtcTime($startTime, config("constants.INTERVIEW_SUPPORT_TICKET.OFFICE_TIMEZONE"));
            $utcEndTime = CommonHelper::getUtcTime($endTime, config("constants.INTERVIEW_SUPPORT_TICKET.OFFICE_TIMEZONE"));

            $tickets = Ticket::select('id', 'user_id', 'ticket_id', 'created_at')
                ->whereBetween('created_at', [$utcStartTime, $utcEndTime])
                ->where('created_at', '>=', $currentUtcDate->subMinutes(config('constants.CRON_TIME.TICKET_MINUTE')))
                ->where('user_id', null)
                ->whereNotIn('status', [config('constants.TICKET.COMPLETE'), config('constants.TICKET.CANCEL')])
                ->get();

            if ($tickets->count() > 0) {
                foreach ($tickets as $key => $value) {
                    if ($currentUtcDate->diffInMinutes($value->created_at) >= config("constants.CRON_TIME.TICKET_MINUTE")) {
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
            }
        } catch (\Exception $ex) {
            CommonHelper::sendCronExceptionMail('WorkingHourTicketNotificationCron', $ex->getMessage(), $ex->getLine());
        }
    }
}
