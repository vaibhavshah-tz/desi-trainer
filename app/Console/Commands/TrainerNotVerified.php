<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\CommonHelper;
use App\Events\SendNotification;
use Carbon\Carbon;
use App\Models\Trainer;
use App\Models\User;

class TrainerNotVerified extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trainer:not-verified';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trainer is not verfied by admin or superadmin, after 24 hourse';

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
     * New trainer is not verified by any admin after 24 hrs
     *
     * @return int
     */
    public function handle()
    {
        try {
            $user = User::getAllUser();
            $trainer = Trainer::select('id', 'status', 'created_at')
                ->whereDate('created_at', '=', Carbon::parse('-24 hours')->toDateString())
                ->where('status', config('constants.NOT_VERIFIED'))
                ->get();

            if ($trainer->count() > 0) {
                foreach ($trainer as $trainerKey => $trainerValue) {
                    foreach ($user as $key => $value) {
                        // Send the notification to super/sub admin
                        $notificationAdminData = [
                            'sender_id' => $trainerValue->id,
                            'receiver_id' => $value->id,
                            'sender_type' => config('constants.NOTIFICATION_TYPE.TRAINER'),
                            'receiver_type' => config('constants.NOTIFICATION_TYPE.USER'),
                            'title' => 'New trainer not verified',
                            'message' => 'New trainer not verified after 24 Hours of registration.',
                            'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.TRAINER'),
                        ];
                        event(new SendNotification($trainerValue, $notificationAdminData));
                    }
                }
            }
        } catch (\Exception $ex) {
            CommonHelper::sendCronExceptionMail('TrainerNotVerified', $ex->getMessage(), $ex->getLine());
        }
    }
}
