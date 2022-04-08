<?php

namespace App\Http\Controllers;

use App\Http\Requests\MeetingRequest;
use App\Models\Ticket;
use App\Models\Meeting;
use App\Models\Timezone;
use Carbon\Carbon;
use CommonHelper;
use Illuminate\Http\Request;
use App\Events\SendNotification;
use App\Events\TicketLog;
use Illuminate\Support\Facades\Auth;

class MeetingController extends Controller
{
    /**
     * Get Ticket listing
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $page_title = 'Meeting Listing';

        return view('meetings.index', compact('page_title'));
    }

    /**
     * Get meetings records with pagination, sorting, searching
     * 
     * @param \Illuminate\Http\Reuest $request
     * @return json
     */
    public function getList(Request $request)
    {
        $tableColumn = [
            'id',
            'admin_name',
            'trainer_name',
            'customer_name',
            'meeting_title',
            'meeting_date_time',
            'timezone_label',
            'meeting_url'
        ];

        //Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page
        $isHistory = 0;

        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $order_arr[0]['column']; // Column index
        $columnName = !empty($tableColumn[$columnIndex]) ? $tableColumn[$columnIndex] : 'meetings.created_at'; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc

        $records = Meeting::select(
            'meetings.id',
            'meetings.ticket_id',
            'meetings.user_id',
            'meetings.customer_id',
            'meetings.trainer_id',
            'meetings.timezone_id',
            'meetings.meeting_title',
            'meetings.date',
            'meetings.time',
            'meetings.meeting_url',
            'meetings.meeting_timestamp',
            'meetings.status',
            'timezones.label as timezone_label',
            \DB::raw("CONCAT(customers.first_name, ' ', customers.last_name) as customer_name"),
            \DB::raw("CONCAT(trainers.first_name, ' ', trainers.last_name) as trainer_name"),
            \DB::raw("CONCAT(users.first_name, ' ', users.last_name) as admin_name"),
            \DB::raw("CONCAT(meetings.date, ' ', meetings.time) as meeting_date_time")
        )
            ->leftJoin('customers', 'meetings.customer_id', '=', 'customers.id')
            ->leftJoin('trainers', 'meetings.trainer_id', '=', 'trainers.id')
            ->leftJoin('users', 'meetings.user_id', '=', 'users.id')
            ->leftJoin('timezones', 'meetings.timezone_id', '=', 'timezones.id')
            ->where('meetings.ticket_id', '=', $request->id)
            ->orderBy($columnName, $columnSortOrder);

        // Search the value
        if (!empty($request->get('history'))) {
            $records->where(function ($query) {
                $query->where('meetings.meeting_timestamp', '<', Carbon::now()->timestamp)
                    ->orWhere('meetings.status', '=', config('constants.MEETING.CANCEL'));
            });
            $isHistory = 1;
        } else {
            $records->active()->where('meetings.meeting_timestamp', '>=', Carbon::now()->timestamp);
        }

        if (!empty($request->get('admin_name'))) {
            $records->where(\DB::raw("CONCAT(users.first_name, ' ', users.last_name)"), 'like', '%' . $request->get('admin_name') . '%');
        }

        if (!empty($request->get('customer_name'))) {
            $records->where(\DB::raw("CONCAT(customers.first_name, ' ', customers.last_name)"), 'like', '%' . $request->get('customer_name') . '%');
        }

        if (!empty($request->get('trainer_name'))) {
            $records->where(\DB::raw("CONCAT(trainers.first_name, ' ', trainers.last_name)"), 'like', '%' . $request->get('trainer_name') . '%');
        }

        if (!empty($request->get('meeting_title'))) {
            $records->where('meetings.meeting_title', 'like', '%' . $request->get('meeting_title') . '%');
        }

        if (!empty($request->get('timezone'))) {
            $records->where('timezones.label', 'like', '%' . $request->get('timezone') . '%');
        }

        if (!empty($request->get('meeting_url'))) {
            $records->where('meetings.meeting_url', 'like', '%' . $request->get('meeting_url') . '%');
        }

        $currentPage = $start / $rowperpage + 1;
        $records = $records->paginate($rowperpage, ["*"], "page", $currentPage);

        return response()->view(
            'meetings.get-list',
            compact(['records', 'rowperpage', 'currentPage', 'draw', 'isHistory'])
        );
    }

    /**
     * Create the specified resourse.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $page_title = 'Create New Meeting';
        $ticket = Ticket::findOrFail($request->id);
        $meetingOptionList = empty($ticket->trainer_id) ? CommonHelper::createMeetingType() : CommonHelper::createMeetingType(true);
        $interestedTrainers = $ticket->interestedTrainers()->active()
            ->select(['trainers.id', \DB::raw("CONCAT(trainers.first_name, ' ',trainers.last_name) as trainer_name")])
            ->orderBy('interested_ticket_trainer.created_at', 'asc')
            ->pluck('trainer_name', 'id');

        return view('meetings.create', compact('page_title', 'meetingOptionList', 'interestedTrainers'));
    }

    /**
     * Store the specified resourse.
     *
     * @param \App\Http\Requests\MeetingRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(MeetingRequest $request)
    {
        try {
            $dateTime = $request->date . ' ' . $request->time;
            $timezone = Timezone::getTimezoneById($request->timezone_id);
            $ticketTime = CommonHelper::getUtcTime($dateTime, $timezone->timezone);
            $currentTime = CommonHelper::convertDateToSpecificTimezone(Carbon::now(), $timezone->timezone);

            if ($dateTime < $currentTime->toDateTimeString()) {
                $request->session()->flash('error', "Date and time must be after current date and time");

                return redirect()->route('tickets.meetings.create', ['id' => $request->id]);
            }
            $ticket = Ticket::findOrFail($request->id);
            if (in_array($request->create_meeting_with, [config('constants.MEETING.CREATE_WITH.ASSIGNED_TRAINER'), config('constants.MEETING.CREATE_WITH.CUSTOMER_AND_ASSIGNED_TRAINER')]) && is_null($ticket->trainer_id)) {
                $request->session()->flash('error', "Trainer is not assigned to this ticket, You can create meeting with customer or interested trainer.");

                return redirect()->route('tickets.meetings.create', ['id' => $request->id]);
            }
            list($customerId, $trainerId, $label) = $this->getMeetingUserData($request, $ticket);

            $request->merge([
                'user_id' => \Auth::id(),
                'customer_id' => $customerId,
                'trainer_id' => $trainerId,
                'ticket_id' => $ticket->id,
                'meeting_timestamp' => $ticketTime->timestamp
            ]);
            $meetings = Meeting::create($request->all());
            if ($meetings) {
                // Send the notification to customer
                if ($customerId) {
                    $notificationDataCustomer = [
                        'sender_id' => Auth::user()->id,
                        'receiver_id' => $customerId,
                        'sender_type' => config('constants.NOTIFICATION_TYPE.USER'),
                        'receiver_type' => config('constants.NOTIFICATION_TYPE.CUSTOMER'),
                        'title' => 'New schedule meeting',
                        'message' => 'Your meeting has been scheduled with your admin. check the link now',
                        'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.MEETING'),
                        'push_notification' => 1
                    ];
                    event(new SendNotification($meetings, $notificationDataCustomer));
                }
                // Send the notification to trainer
                if ($trainerId) {
                    $notificationDataTrainer = [
                        'sender_id' => Auth::user()->id,
                        'receiver_id' => $trainerId,
                        'sender_type' => config('constants.NOTIFICATION_TYPE.USER'),
                        'receiver_type' => config('constants.NOTIFICATION_TYPE.TRAINER'),
                        'title' => 'New schedule meeting',
                        'message' => 'Your meeting has been scheduled with your admin. check the link now',
                        'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.MEETING'),
                        'push_notification' => 1
                    ];
                    event(new SendNotification($meetings, $notificationDataTrainer));
                }
                // Save ticket log
                // $label = ($customerName) ? 'customer ' . $customerName : 'trainer ' . $trainerName;
                // $label = ($label) ? $label : 'both customer and trainer';
                event(new TicketLog($meetings, \Auth::user()->full_name . ' create meeting with ' . $label));

                $request->session()->flash('success', 'Meeting created successfully.');

                return redirect()->route('meetings', ['id' => $request->id]);
            }

            $request->session()->flash('error', 'Meeting can not be created, Please try again.');

            return redirect()->route('meetings', ['id' => $request->id]);
        } catch (\Exception $ex) {
            $request->session()->flash('error', 'Something went wrong, Please try again.');

            return redirect()->route('meetings', ['id' => $request->id]);
        }
    }

    /**
     * Cancel the specified meeting.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request)
    {
        try {
            $meeting = Meeting::findorfail($request->meeting_id);
            $meeting->status = config('constants.MEETING.CANCEL');
            if ($meeting->save()) {
                event(new TicketLog($meeting, $meeting->meeting_title . ' meeting is cancel by ' . \Auth::user()->full_name));
                $request->session()->flash('success', 'Meeting canceled successfully.');
            } else {
                $request->session()->flash('error', 'Meeting can not be canceled, Please try again.');
            }

            return redirect()->route('meetings', ['id' => $request->id]);
        } catch (\Exception $ex) {
            $request->session()->flash('error', 'Something went wrong, Please try again.');

            return redirect()->route('meetings', ['id' => $request->id]);
        }
    }

    /**
     * Check trainer assigned in ticket
     */
    public function checkTrainer(Request $request)
    {
        try {
            if ($request->ajax() && !empty($request->create_meeting_with) && in_array($request->create_meeting_with, [config('constants.MEETING.CREATE_WITH.ASSIGNED_TRAINER'), config('constants.MEETING.CREATE_WITH.CUSTOMER_AND_ASSIGNED_TRAINER')])) {
                $data = Ticket::whereNotNull('trainer_id')->findOrFail($request->ticket_id);

                return ($data) ? 'true' : 'false';
            }

            return 'true';
        } catch (\Exception $ex) {
            return 'false';
        }
    }

    /**
     * Get meeting user data
     */
    public function getMeetingUserData(Request $request, $ticket) {
        $label = '';

        switch ($request->create_meeting_with) {
            case config('constants.MEETING.CREATE_WITH.CUSTOMER'):
                $customerId = $ticket->customer_id;
                $trainerId = null;
                $label = 'customer '. $ticket->customer->full_name;
                break;
            case config('constants.MEETING.CREATE_WITH.ASSIGNED_TRAINER'):
                $customerId = null;
                $trainerId = $ticket->trainer_id;
                $label = 'assigned trainer '. $ticket->trainer->full_name;
                break;
            case config('constants.MEETING.CREATE_WITH.CUSTOMER_AND_ASSIGNED_TRAINER'):
                $customerId = $ticket->customer_id;
                $trainerId = $ticket->trainer_id;
                $label = 'both customer and assigned trainer';
                break;
            case config('constants.MEETING.CREATE_WITH.INTERESTED_TRAINER'):
                $customerId = null;
                $trainerId = $request->interested_trainer_id;
                $label = 'interested trainer';
                break;
            case config('constants.MEETING.CREATE_WITH.CUSTOMER_AND_INTERESTED_TRAINER'):
                $customerId = $ticket->customer_id;
                $trainerId = $request->interested_trainer_id;
                $label = 'both customer and interested trainer';
                break;
            default:
                $customerId = null;
                $trainerId = null;
                break;
        }

        return [$customerId, $trainerId, $label];
    }
}
