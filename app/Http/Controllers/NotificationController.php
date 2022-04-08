<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Auth;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Show all notification
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $page_title = 'Notifications';
        $notifications = Notification::getNotifications()->where('receiver_id', Auth::user()->id);

        $notifications = $notifications->paginate(10);
        if ($request->ajax()) {
            $view = view('notifications._data', compact('notifications'))->render();

            return response()->json(['html' => $view]);
        }
        return view('notifications.index', compact(['page_title', 'notifications']));
    }

    /**
     * Update the status to read notification
     * 
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function updateRead(Request $request)
    {
        if ($request->ajax()) {
            $notification = Notification::findorfail($request->id);
            if ($notification->update([
                'is_read' => config('constants.READ_NOTIFICATION')
            ])) {
                return true;
            }
            return false;
        }

        return false;
    }
}
