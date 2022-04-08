<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\CommonHelper;
use App\Events\SendNotification;
use Carbon\Carbon;
use App\Models\Ticket;

class GlobalRequestTicket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ticket:global-request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the notification to trainer with matching skills, when ticket is created';

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
     * Check the newly create ticket
     * check the ticket skill with trainer and send the notification to associated trainer
     *
     * @return int
     */
    public function handle()
    {
        try {
            $trainerIds = [];
            $currentUtcDate = Carbon::now();

            $ticketDetails = Ticket::select('id', 'user_id', 'trainer_id', 'created_at', 'status')
                ->where('created_at', '>=', $currentUtcDate->subMinutes(config('constants.CRON_TIME.GLOBAL_REQUEST_NEW_TICKET')))
                ->with([
                    'primarySkills:id',
                    'primarySkills.trainer:id',
                ])
                ->whereNotIn('status', [config('constants.TICKET.COMPLETE'), config('constants.TICKET.CANCEL')])
                ->get();
            if ($ticketDetails->count() > 0) {
                foreach ($ticketDetails as $keyTicket => $valueTicket) {
                    foreach ($valueTicket->primarySkills as $keySkill => $valueSkill) {
                        foreach ($valueSkill->trainer as $key => $value) {
                            if (!in_array($value->id, $trainerIds)) {
                                echo $value->id;
                                array_push($trainerIds, $value->id);
                                $notificationDataTrainer = [
                                    'sender_id' => $valueTicket->user_id,
                                    'receiver_id' => $value->id,
                                    'sender_type' => config('constants.NOTIFICATION_TYPE.USER'),
                                    'receiver_type' => config('constants.NOTIFICATION_TYPE.TRAINER'),
                                    'title' => 'New global request created',
                                    'message' => 'New ticket is created in global request',
                                    'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.GLOBAL_REQUEST'),
                                    'push_notification' => 1
                                ];
                                event(new SendNotification($valueTicket, $notificationDataTrainer));
                            }
                        }
                    }
                    $trainerIds = [];
                }
            }
        } catch (\Exception $ex) {
            CommonHelper::sendCronExceptionMail('GlobalRequestTicket', $ex->getMessage(), $ex->getLine());
        }
    }
}
