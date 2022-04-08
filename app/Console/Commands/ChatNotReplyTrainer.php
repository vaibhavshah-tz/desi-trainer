<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\CommonHelper;
use App\Events\SendNotification;
use App\Models\ChatRoom;
use Carbon\Carbon;

class ChatNotReplyTrainer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:not-reply-trainer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Not reply to staff after 10 min for chat for trainer.';

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
     * Customer did'nt replay in 15 min
     * send the notification
     * 
     * @return int
     */
    public function handle()
    {
        try {
            $currentUtcDate = Carbon::now();
            $chatRoom = ChatRoom::where([
                ['user_id', '<>', null],
                ['trainer_id', '<>', null]
            ])->with(['chatMessages' => function ($q) {
                $q->select('id', 'chat_room_id', 'sender_type', 'created_at')
                    ->latest()->first();
            }])->get();

            if ($chatRoom->count() > 0) {
                foreach ($chatRoom as $key => $value) {
                    $chatMessage = $value->chatMessages->first();
                    if (!empty($chatMessage) && $chatMessage->sender_type != config('constants.SENDER_TYPE.TRAINER') && $currentUtcDate->diffInMinutes($chatMessage->created_at) <= config("constants.CRON_TIME.CHAT_NOT_RPY_MINUTE")) {
                        $notificationData = [
                            'sender_id' => config('constants.ADMIN_ROLE.SUPER_ADMIN'),
                            'receiver_id' => $value->trainer_id,
                            'sender_type' => config('constants.NOTIFICATION_TYPE.USER'),
                            'receiver_type' => config('constants.NOTIFICATION_TYPE.TRAINER'),
                            'title' => 'New message',
                            'message' => 'New message found, please click to check',
                            'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.CHAT'),
                            'push_notification' => 1
                        ];
                        event(new SendNotification($value, $notificationData));
                    }
                }
            }
        } catch (\Exception $ex) {
            CommonHelper::sendCronExceptionMail('ChatNotReply', $ex->getMessage(), $ex->getLine());
        }
    }
}
