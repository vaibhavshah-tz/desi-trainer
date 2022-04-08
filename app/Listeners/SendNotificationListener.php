<?php

namespace App\Listeners;

use App\Events\SendNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Notification;
use App\Models\UserDevice;


class SendNotificationListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SendNotification  $event
     * @return void
     */
    public function handle(SendNotification $event)
    {
        $pushNotificationRes = '';
        $data = $event->data;
        if (config('constants.NOTIFICATION_REDIRECTION_TYPE.PROPOSAL') === $data['redirection_type']) {
            $proposalName = $data['proposal_name'];
            unset($data['proposal_name']);
        }
        $notification = $event->model->notifications()->create($data);

        if (!empty($data['push_notification'])) {
            unset($data['push_notification']);
            $userDevice = UserDevice::getDeviceToken($data['receiver_id'], $data['receiver_type']);
            if (config('constants.NOTIFICATION_REDIRECTION_TYPE.PROPOSAL') === $notification->redirection_type) {
                $notification->proposal_name = $proposalName ?? '';
            }
            if (!empty($userDevice)) {
                switch ($userDevice->device_type) {
                    case 1:
                        $pushNotificationRes = $this->androidPushNotification($userDevice->device_token, $notification);
                        break;
                    case 2:
                        $pushNotificationRes = $this->iosPushNotification($userDevice->device_token, $notification);
                        break;
                    default:
                        return true;
                        break;
                }
                unset($notification->proposal_name);
            }
        }
        if ($pushNotificationRes) {
            $notification->update([
                'push_notification' => $pushNotificationRes
            ]);
        }
    }

    /**
     * Send the push notification for android
     * 
     * @param $deviceToken
     * @param $data
     * @return json
     */
    public function androidPushNotification($deviceToken, $data)
    {
        $registrationIds = [$deviceToken];
        $notificationData = [
            'title'    => $data->title,
            'body'  => $data->message,
            'sound' => true
        ];
        $msgData = [
            'redirection_type' => $data->redirection_type,
            'redirection_id' => !empty($data->redirection_id) ? $data->redirection_id : '',
            'notification_id' => $data->id,
            'ticket_id' => !empty($data->ticket_id) ? $data->ticket_id : '',
            'ticket_status' => !empty($data->ticket_status) ? $data->ticket_status : '',
            'ticket_status_label' => !empty($data->ticket_status_label) ? $data->ticket_status_label : '',
            'receiver_id' => !empty($data->receiver_id) ? $data->receiver_id : '',
            'receiver_type' => !empty($data->receiver_type) ? $data->receiver_type : '',
            'proposal_name' => !empty($data->proposal_name) ? $data->proposal_name : ''
        ];

        $fields = [
            'registration_ids' => $registrationIds,
            'notification'    => $notificationData,
            'data' => $msgData
        ];
        $headers = [
            'Authorization: key=' . config('constants.FCM_SERVER_KEY'),
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            return curl_error($ch);
        }
        curl_close($ch);

        return $result;
    }

    /**
     * Send the push notification for IOS device
     * 
     * @param $deviceToken
     * @param $data
     * @return json
     */
    public function iosPushNotification($deviceToken, $data)
    {
        $registrationIds = [$deviceToken];
        $notificationData = [
            'title'    => $data->title,
            'body'  => $data->message,
            'sound' => true
        ];
        $msgData = [
            'redirection_type' => $data->redirection_type,
            'redirection_id' => !empty($data->redirection_id) ? $data->redirection_id : '',
            'notification_id' => $data->id,
            'ticket_id' => !empty($data->ticket_id) ? $data->ticket_id : '',
            'ticket_status' => !empty($data->ticket_status) ? $data->ticket_status : '',
            'ticket_status_label' => !empty($data->ticket_status_label) ? $data->ticket_status_label : '',
            'receiver_id' => !empty($data->receiver_id) ? $data->receiver_id : '',
            'receiver_type' => !empty($data->receiver_type) ? $data->receiver_type : '',
            'proposal_name' => !empty($data->proposal_name) ? $data->proposal_name : ''
        ];

        $fields = [
            'registration_ids' => $registrationIds,
            'notification'    => $notificationData,
            'data' => $msgData,
            'priority' => 'high'
        ];

        $json = json_encode($fields);

        $headers = [];
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key=' . config('constants.FCM_SERVER_KEY');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Send the request
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
        }

        curl_close($ch);

        return $result;
    }
}
