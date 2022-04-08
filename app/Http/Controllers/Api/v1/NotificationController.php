<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends ApiController
{

    /**
     * URL - {{local}}/v1/notifications?limit=10
     * METHOD - GET
     * Get all notifications
     * 
     * @param \Illuminate\Http\Request $request
     * @return object|JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('limit', 10);
            $loggedinId = '';
            $receiverType = '';
            if (Auth::guard('trainer')->check()) {
                $loggedinId = Auth::guard('trainer')->user()->id;
                $receiverType = config('constants.NOTIFICATION_TYPE.TRAINER');
            } else {
                $loggedinId = Auth::guard('customer')->user()->id;
                $receiverType = config('constants.NOTIFICATION_TYPE.CUSTOMER');
            }
            $notifications = Notification::select('id', 'notificationtable_id', 'notificationtable_type', 'receiver_id', 'receiver_type', 'title', 'message', 'redirection_type', 'created_at', 'is_read')
                // ->with('notificationtable')
                ->where([
                    ['receiver_id', $loggedinId],
                    ['receiver_type', $receiverType],
                ])->orderBy('created_at', 'desc');

            return $this->apiResponse->respondWithMessageAndPayload($notifications->paginate($perPage));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * URL - {{local}}/v1/notification/update-read
     * METHOD - PUT|PATCH
     * Update notification status
     * 
     * @param \Illuminate\Http\Request $request
     * @return object|JsonResponse
     */
    public function updateStatus(Request $request)
    {
        try {
            if (empty($request->id)) {
                return $this->apiResponse->respondWithError(__("Please enter notification id"));
            }
            $notification = Notification::findorfail($request->id);
            if ($notification->update([
                'is_read' => config('constants.READ_NOTIFICATION')
            ])) {
                return $this->apiResponse->respondWithMessageAndPayload($notification, __("Notification is mark as read"));
            }

            return $this->apiResponse->respondWithError(__("Notification is not mark as read, Please try again"));
        } catch (\Exception $exception) {
            return $this->apiResponse->handleAndResponseException($exception);
        }
    }

    /**
     * URL - {{local}}/v1/notifications/unread-count
     * METHOD - GET
     * Check un read notification count
     * 
     * @return object|JsonResponse
     */
    public function checkUnreadNotification()
    {
        try {
            $loggedinId = '';
            $receiverType = '';
            if (Auth::guard('trainer')->check()) {
                $loggedinId = Auth::guard('trainer')->user()->id;
                $receiverType = config('constants.NOTIFICATION_TYPE.TRAINER');
            } else {
                $loggedinId = Auth::guard('customer')->user()->id;
                $receiverType = config('constants.NOTIFICATION_TYPE.CUSTOMER');
            }
            $notifications = Notification::where([
                ['receiver_id', $loggedinId],
                ['receiver_type', $receiverType],
                ['is_read', config('constants.UNREAD_NOTIFICATION')],
            ])->count();
            $payload['is_read'] = ($notifications) ? true : false;
            $payload['count'] = $notifications;

            return $this->apiResponse->respondWithMessageAndPayload($payload, __("Read notification status"));
        } catch (\Exception $exception) {
            return $this->apiResponse->handleAndResponseException($exception);
        }
    }
}
