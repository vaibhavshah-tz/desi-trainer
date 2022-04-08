<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Meeting;
use App\Models\Ticket;
use App\Models\Timezone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\MeetingRequest;
use CommonHelper;
use Carbon\Carbon;
use App\Events\TicketLog;
use App\Events\SendNotification;

class MeetingController extends ApiController
{

    /**
     * URL - {{local}}/v1/meeting/all-meeting-list?limit=10
     * METHOD - GET
     * Get the all upcoming and history of meetings
     * 
     * @param \Illuminate\Http\Request $request
     * @return object|JsonResponse
     */
    public function allMeetingListing(Request $request)
    {
        try {
            $perPage = $request->get('limit', 10);
            $loggedinId = '';
            $userDetail = '';
            $userCondition = $meeting = [];
            if (Auth::guard('trainer')->check()) {
                $loggedinId = Auth::guard('trainer')->user()->id;
                $userDetail = 'customer:id,first_name,last_name,avatar';
                $userCondition = ['trainer_id', '=', $loggedinId];
            } else {
                $loggedinId = Auth::guard('customer')->user()->id;
                $userDetail = 'trainer:id,first_name,last_name,avatar';
                $userCondition = ['customer_id', '=', $loggedinId];
            }
            $meeting = Meeting::active()->select(
                'id',
                'user_id',
                'ticket_id',
                'customer_id',
                'trainer_id',
                'timezone_id',
                'meeting_title',
                'date',
                'time',
                'meeting_url',
                'meeting_timestamp',
                'status',
                \DB::raw('(CASE
                        WHEN meetings.meeting_timestamp >= ' . Carbon::today()->timestamp . ' THEN "Scheduled"
                        ELSE ""
                        END) AS status_lable')
            )
                ->with([
                    $userDetail,
                    'timezone:id,label,offset,timezone,abbreviation',
                    'ticket:id,course_category_id,course_id,other_course',
                    'ticket.courseCategory:id,name',
                    'ticket.course:id,name,cover_image',
                    'user:id,first_name,last_name'
                ])
                ->where([$userCondition])
                ->where('meetings.meeting_timestamp', '>=', Carbon::today()->timestamp)
                ->orderBy('created_at', 'desc');

            return $this->apiResponse->respondWithMessageAndPayload($meeting->paginate($perPage));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * URL - {{local}}/v1/meeting/ticket-meeting-list?ticket_id=1
     * METHOD - GET
     * Get the all upcoming and history of meetings
     * 
     * @param \Illuminate\Http\Request $request
     * @return object|JsonResponse
     */
    public function ticketMeetingListing(Request $request)
    {
        try {
            $loggedinId = '';
            $userDetail = '';
            $userCondition = $meeting = [];
            $ticketId = $request->get('ticket_id');
            if (Auth::guard('trainer')->check()) {
                $loggedinId = Auth::guard('trainer')->user()->id;
                $userDetail = 'customer:id,first_name,last_name,avatar';
                $userCondition = ['trainer_id', '=', $loggedinId];
            } else {
                $loggedinId = Auth::guard('customer')->user()->id;
                $userDetail = 'trainer:id,first_name,last_name,avatar';
                $userCondition = ['customer_id', '=', $loggedinId];
            }

            $meeting['upcoming_meetings'] = Meeting::active()->select(
                'id',
                'user_id',
                'ticket_id',
                'customer_id',
                'trainer_id',
                'timezone_id',
                'meeting_title',
                'date',
                'time',
                'meeting_url',
                'meeting_timestamp',
                'status',
                \DB::raw('(CASE
                        WHEN meetings.meeting_timestamp >= ' . Carbon::today()->timestamp . ' THEN "Scheduled"
                        ELSE ""
                        END) AS status_lable')
            )
                ->with([
                    $userDetail,
                    'timezone:id,label,offset,timezone,abbreviation',
                    'user:id,first_name,last_name'
                ])
                ->where('meeting_timestamp', '>=', Carbon::today()->timestamp)
                ->where([$userCondition])
                ->where('ticket_id', $ticketId)
                ->orderBy('created_at', 'desc')->get();

            $meeting['history_meetings'] = Meeting::select('id', 'user_id', 'ticket_id', 'customer_id', 'trainer_id', 'timezone_id', 'meeting_title', 'date', 'time', 'meeting_url', 'meeting_timestamp', 'status')
                ->with([
                    $userDetail,
                    'timezone:id,label,offset,timezone,abbreviation',
                    'user:id,first_name,last_name'
                ])
                ->where(function ($query) {
                    $query->where('meetings.meeting_timestamp', '<', Carbon::now()->timestamp)
                        ->orWhere('meetings.status', '=', config('constants.MEETING.CANCEL'));
                })
                ->where([$userCondition])
                ->where('ticket_id', $ticketId)
                ->orderBy('created_at', 'desc')->get();

            return $this->apiResponse->respondWithMessageAndPayload($meeting);
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * URL - {{local}}/v1/meeting/create
     * METHOD - POST
     * Create a new tickets
     * 
     * @param \Illuminate\Http\Request $request
     * @return object|JsonResponse
     */
    public function createMeeting(MeetingRequest $request)
    {
        try {
            $dateTime = $request->date . ' ' . $request->time;
            $timezone = Timezone::getTimezoneById($request->timezone_id);
            $ticketTime = CommonHelper::getUtcTime($dateTime, $timezone->timezone);
            $currentTime = CommonHelper::convertDateToSpecificTimezone(Carbon::now(), $timezone->timezone);
            $ticket = Ticket::findOrFail($request->ticket_id);
            if (in_array($ticket->status, [config('constants.TICKET.COMPLETE'), config('constants.TICKET.INACTIVE'), config('constants.TICKET.CANCEL')])) {
                $message = in_array($ticket->status, [config('constants.TICKET.COMPLETE'), config('constants.TICKET.INACTIVE'), config('constants.TICKET.CANCEL')]) ? strtolower($ticket->status_label) : '';

                return $this->apiResponse->respondWithError(__("Can not create meeting as this ticket is " . $message));
            }
            if ($dateTime < $currentTime->toDateTimeString()) {
                return $this->apiResponse->respondWithError(__("Date and time must be after current date"));
            }
            $request->merge(['trainer_id' => Auth::guard('trainer')->user()->id, 'meeting_timestamp' => $ticketTime->timestamp]);
            $meeting = Meeting::create($request->all());
            if ($meeting) {
                $notificationDataCustomer = [
                    'sender_id' => $ticket->trainer_id,
                    'receiver_id' => $ticket->customer_id,
                    'sender_type' => config('constants.NOTIFICATION_TYPE.TRAINER'),
                    'receiver_type' => config('constants.NOTIFICATION_TYPE.CUSTOMER'),
                    'title' => 'New schedule',
                    'message' => 'Your meeting has been scheduled with your trainer. check the link now',
                    'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.MEETING'),
                    'push_notification' => 1
                ];
                // Send the notification to customer
                event(new SendNotification($meeting, $notificationDataCustomer));
                // Save ticket log
                event(new TicketLog($meeting, $meeting->trainer->full_name . ' created meeting with ' . $meeting->customer->full_name));

                return $this->apiResponse->respondWithMessageAndPayload($meeting, __("Meeting has been create successfully"));
            }

            return $this->apiResponse->respondWithError(__("Meeting has been not create successfully, Please try again"));
        } catch (\Exception $exception) {
            return $this->apiResponse->handleAndResponseException($exception);
        }
    }

    /**
     * Set the chat read unread message
     * URL - {{local}}/v1/trainer/meeting/mark-as-read
     * Method - POST
     * 
     * @param \App\Http\Requests\StoreCustomer $request
     * @return JsonResponse
     */
    public function markAsRead(Request $request)
    {
        try {
            if (Auth::guard('trainer')->check()) {
                $loggedinId = Auth::guard('trainer')->user()->id;
                $userCondition = ['trainer_id', '=', $loggedinId];
                $meetingType = ['trainer_is_read' => config('constants.READ_NOTIFICATION')];
            } else {
                $loggedinId = Auth::guard('customer')->user()->id;
                $userCondition = ['customer_id', '=', $loggedinId];
                $meetingType = ['customer_is_read' => config('constants.READ_NOTIFICATION')];
            }
            if (isset($request->ticket_id)) {
                $meetings = Meeting::where([
                    $userCondition,
                    ['ticket_id', $request->ticket_id]
                ])->update($meetingType);
            } else {
                $meetings = Meeting::where([
                    $userCondition
                ])->update($meetingType);
            }

            if ($meetings) {
                return $this->apiResponse->respondWithMessageAndPayload($meetings, __("Meeting mark as read successfully"));
            }

            return $this->apiResponse->respondWithError(__("Something went wrong!"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }
}
