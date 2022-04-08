<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use LRedis;
use App\Traits\Chat;
use App\Helpers\CommonHelper;

class SubscribeChat extends Command
{
    use Chat;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscribe:chat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe to a Redis channel for chat message';

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
     *
     * @return int
     */
    public function handle()
    {
        try {
            $redis = LRedis::connection('default');
            $publishDefault = LRedis::connection('publish-default');
            // if (empty($publishDefault->connect()) && empty($redis->connect())) {
            $publishDefault->psubscribe(['save-data'], function ($message, $channel) use ($redis) {
                $payload = json_decode($message, true);
                if (!empty($payload)) {
                    $saveData = $this->saveMessages($payload);
                    $redis->publish($saveData['room_id'], json_encode($saveData));
                }
            });
            // }
        } catch (\Exception $ex) {            
            CommonHelper::sendCronExceptionMail('SubscribeChat', $ex->getMessage(), $ex->getLine());
        }
    }
}
