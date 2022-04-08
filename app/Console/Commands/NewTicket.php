<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\CommonHelper;
use App\Events\SendNotification;
use Carbon\Carbon;
use App\Models\Ticket;
use App\Models\User;

class NewTicket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'new:ticket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'when new ticket is created, so super and sub admin will notified';

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
     * Get the new ticket and send the notification to super and sub admin
     *
     * @return int
     */
    public function handle()
    {
        try {
            $currentUtcDate = Carbon::now();
            $user = User::getAllUser();

            $ticketDetails = Ticket::select('id', 'customer_id', 'status', 'created_at')
                ->where('created_at', '>=', $currentUtcDate->subMinutes(config('constants.CRON_TIME.NEW_TICKET')))
                ->where('status', config('constants.TICKET.NEW'))
                ->whereNotIn('status', [config('constants.TICKET.COMPLETE'), config('constants.TICKET.CANCEL')])
                ->get();
            if ($ticketDetails->count() > 0) {
                foreach ($ticketDetails as $ticketKey => $ticketvalue) {
                    foreach ($user as $key => $value) {
                        // Send the notification to super/sub admin
                        $notificationAdminData = [
                            'sender_id' => $ticketvalue->customer_id,
                            'receiver_id' => $value->id,
                            'sender_type' => config('constants.NOTIFICATION_TYPE.CUSTOMER'),
                            'receiver_type' => config('constants.NOTIFICATION_TYPE.USER'),
                            'title' => 'New ticket created',
                            'message' => 'New ticket is created by customer',
                            'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.TICKET'),
                        ];
                        event(new SendNotification($ticketvalue, $notificationAdminData));
                    }
                }
            }
        } catch (\Exception $ex) {
            CommonHelper::sendCronExceptionMail('NewTicket', $ex->getMessage(), $ex->getLine());
        }
    }
}
