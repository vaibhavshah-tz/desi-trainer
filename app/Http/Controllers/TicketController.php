<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignAdminRequest;
use App\Http\Requests\TicketRequest;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Timezone;
use App\Models\PrimarySkill;
use App\Models\CourseCategory;
use App\Models\Course;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use CommonHelper;
use Carbon\Carbon;
use App\Events\TicketLog;
use App\Events\SendNotification;

class TicketController extends Controller
{
    /**
     * Get Ticket listing
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $page_title = 'Ticket Listing';
        $ticketType = json_encode(array_column(TicketType::getAllTicketType(), 'name', 'id'));

        return view('tickets.index', compact('page_title', 'ticketType'));
    }

    /**
     * Get Assigned Ticket listing
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function assignedTicket(Request $request)
    {
        $page_title = 'Assigned Ticket Listing';
        $ticketType = json_encode(array_column(TicketType::getAllTicketType(), 'name', 'id'));

        return view('tickets.assigned-ticket', compact('page_title', 'ticketType'));
    }

    /**
     * Get all tickets records with pagination, sorting, searching
     * 
     * @param \Illuminate\Http\Reuest $request
     * @return json
     */
    public function getList(Request $request)
    {
        if (!empty($request->get('assigned'))) {
            $tableColumn = [
                // 'id',
                'ticket_id',
                'ticket_type_name',
                'course_name',
                'trainer_name',
                'formated_created_at',
                'ticket_date_time',
                'status'
            ];
        } else {
            $tableColumn = [
                // 'id',
                'ticket_id',
                'ticket_type_name',
                'course_name',
                'admin_name',
                'customer_name',
                'trainer_name',
                'formated_created_at',
                'ticket_date_time',
                'interested_trainers_count',
                'status'
            ];
        }

        //Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');

        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        $userType = $request->get('type');
        $userId = $request->get('user');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index

        $ticketIdSearch = $columnName_arr[array_search('ticket_id', $tableColumn)]['search']['value'];
        $ticketTypeSearch = $columnName_arr[array_search('ticket_type_name', $tableColumn)]['search']['value'];
        $courseNameSearch = $columnName_arr[array_search('course_name', $tableColumn)]['search']['value'];
        $trainerNameSearch = $columnName_arr[array_search('trainer_name', $tableColumn)]['search']['value'];
        $createdDateSearch = $columnName_arr[array_search('formated_created_at', $tableColumn)]['search']['value'];
        $statusSearch = $columnName_arr[array_search('status', $tableColumn)]['search']['value'];

        if (empty($request->get('assigned'))) {
            $adminNameSearch = $columnName_arr[array_search('admin_name', $tableColumn)]['search']['value'];
            $customerNameSearch = $columnName_arr[array_search('customer_name', $tableColumn)]['search']['value'];
        }
        $columnName = ($columnName_arr[$columnIndex]['data'] === 'formated_created_at') ? 'created_at' : $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        $records = Ticket::select(
            'tickets.id',
            'tickets.ticket_type_id',
            'tickets.user_id',
            'tickets.customer_id',
            'tickets.course_id',
            'tickets.ticket_id',
            'tickets.timezone_id',
            'tickets.date',
            'tickets.time',
            'tickets.ticket_timestamp',
            'tickets.created_at',
            'tickets.trainer_id',
            'tickets.status',
            'tickets.other_course',
            'tickets.is_global',
            'ticket_types.name as ticket_type_name',
            'courses.name as course_name',
            \DB::raw('TIMESTAMPDIFF(minute, tickets.created_at, now()) as duration'),
            \DB::raw("CONCAT(users.first_name, ' ', users.last_name) as admin_name"),
            \DB::raw("CONCAT(trainers.first_name, ' ', trainers.last_name) as trainer_name"),
            \DB::raw("CONCAT(customers.first_name, ' ', customers.last_name) as customer_name")
        )
            ->leftJoin('ticket_types', 'tickets.ticket_type_id', '=', 'ticket_types.id')
            ->leftJoin('trainers', 'tickets.trainer_id', '=', 'trainers.id')
            ->leftJoin('customers', 'tickets.customer_id', '=', 'customers.id')
            ->leftJoin('courses', 'tickets.course_id', '=', 'courses.id')
            ->leftJoin('users', 'tickets.user_id', '=', 'users.id')
            ->with([
                'timezone:id,timezone,label,abbreviation',
                'proposals' => function ($q) {
                    $q->select('id', 'ticket_id')
                        ->withCount(['trainers' => function ($query) {
                            $query->where('action', config('constants.PROPOSAL.ACCEPTED'));
                        }]);
                }
            ])
            ->withCount('interestedTrainers')
            ->orderBy($columnName, $columnSortOrder);

        // Search the value
        if (!empty($userType) && !empty($userId)) {
            switch ($userType) {
                case 1:
                    $records->where('tickets.customer_id', $userId);
                    break;
                case 2:
                    $records->where('tickets.trainer_id', $userId);
                    break;
            }
        }

        if (!empty($request->get('assigned'))) {
            $records->where('tickets.user_id', \Auth::id());
        }

        if (!empty($ticketTypeSearch)) {
            // $records->where('ticket_types.name', 'like', '%' . $ticketTypeSearch . '%');
            $records->where('tickets.ticket_type_id',  $ticketTypeSearch);
        }

        if (!empty($ticketIdSearch)) {
            $records->where('tickets.ticket_id', 'like', '%' . trim($ticketIdSearch) . '%');
        }

        if (!empty($courseNameSearch)) {
            $records->where(function ($q) use ($courseNameSearch) {
                $q->where('courses.name', 'like', '%' . $courseNameSearch . '%')
                    ->orWhere('tickets.other_course', 'like', '%' . $courseNameSearch . '%');
            });
        }

        if (!empty($adminNameSearch)) {
            $records->where(\DB::raw("CONCAT(users.first_name, ' ', users.last_name)"), 'like', '%' . $adminNameSearch . '%');
        }

        if (!empty($customerNameSearch)) {
            $records->where(\DB::raw("CONCAT(customers.first_name, ' ', customers.last_name)"), 'like', '%' . $customerNameSearch . '%');
        }

        if (!empty($trainerNameSearch)) {
            $records->where(\DB::raw("CONCAT(trainers.first_name, ' ', trainers.last_name)"), 'like', '%' . $trainerNameSearch . '%');
        }

        if (!empty($createdDateSearch)) {
            $records->where('tickets.created_at', 'like', '%' . $createdDateSearch . '%');
        }

        if ($statusSearch != '') {
            $records->where('tickets.status', intval($statusSearch));
        }
        $currentPage = $start / $rowperpage + 1;
        $records = $records->paginate($rowperpage, ["*"], "page", $currentPage)->toArray();
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $records['per_page'],
            "iTotalDisplayRecords" => $records['total'],
            "aaData" => $records['data']
        );

        return response()->json($response, 200);
    }

    /**
     * Get Assigned admin to the ticket
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function getAssignedAdmin(Request $request)
    {
        try {
            $ticket = Ticket::findOrFail($request->id);
            $users = User::getSubAdminList();

            return view('tickets._form', compact('ticket', 'users'));
        } catch (\Exception $ex) {
            return response()->json(['error' => 'Something went wrong, Please try again!'], 500);
        }
    }

    /**
     * Assign admin to the ticket
     * 
     * @param \App\Http\Requests\AssignAdminRequest $request
     * @return \Illuminate\Http\Response
     */
    public function assignAdmin(AssignAdminRequest $request)
    {
        try {
            $message = '';
            $ticket = Ticket::findOrFail($request->ticket_id);
            $userId = 0;
            if (!empty($request->user_id)) {
                $userId = $request->user_id;
                $message = ' by super admin';
            } elseif (!empty($request->assign_to_self)) {
                $userId = \Auth::id();
                $message = ' by self';
            }
            $ticket->user_id = $userId;
            $ticket->status = ($ticket->status === config('constants.TICKET.IN_PROGRESS')) ? config('constants.TICKET.IN_PROGRESS') : config('constants.TICKET.PENDING');

            if ($ticket->save()) {
                // Save ticket log
                event(new TicketLog($ticket, 'Ticket has been assigned to admin ' . $ticket->user->full_name . $message));
                // Send the notification to customer
                $notificationDataCustomer = [
                    'sender_id' => \Auth::user()->id,
                    'receiver_id' => $ticket->customer_id,
                    'sender_type' => config('constants.NOTIFICATION_TYPE.USER'),
                    'receiver_type' => config('constants.NOTIFICATION_TYPE.CUSTOMER'),
                    'title' => 'Ticket status change',
                    'message' => 'Ticket status has been updated to ' . $ticket->status_label,
                    'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.TICKET'),
                    'push_notification' => 1
                ];
                event(new SendNotification($ticket, $notificationDataCustomer));

                $request->session()->flash('success', 'Admin assigned successfully.');

                return redirect()->route('tickets');
            }
            $request->session()->flash('error', 'Admin can not be assigned, Please try again.');

            return redirect()->route('tickets');
        } catch (\Exception $ex) {
            $request->session()->flash('error', 'Something went wrong, Please try again.');

            return redirect()->route('tickets');
        }
    }

    /**
     * Delete specific record
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $id)
    {
        try {
            if ($request->ajax()) {
                if (!empty($request->check_allowance)) {
                    $ticket = Ticket::where([
                        ['id', '=', $id],
                        ['status', '<>', config('constants.TICKET.COMPLETE')],
                        ['status', '<>', config('constants.TICKET.CANCEL')],
                        ['user_id', '<>', null],
                    ])->count();

                    return $ticket;
                }

                $ticket = Ticket::findOrFail($request->id);

                if ($ticket->delete()) {
                    $response = ['status' => 1, 'message' => 'Ticket deleted successfully.'];
                } else {
                    $response = ['status' => 0, 'message' => 'Ticket not deleted, Please try again.'];
                }

                return response()->json($response);
            }
        } catch (\Exception $ex) {
            $response = ['status' => 0, 'message' => 'Something went wrong, Please try again!'];

            return response()->json($response, 500);
        }
    }

    /**
     * View the all ticket details with fillable form
     * 
     * @param $request
     * @return form
     */
    public function view(Request $request)
    {
        $page_title = 'Ticket view details';
        $users = User::getSubAdminList(true);
        $ticketDetails = Ticket::with([
            'user:id,first_name,last_name',
            'ticketType:id,name',
            'course:id,name',
            'primarySkills',
            'timezone:id,label',
            'customer:id,first_name,last_name,email,user_type',
            'ticketEmployees:id,ticket_id,employee_name,country_code,phone_number,email',
            'trainer:id,first_name,last_name',
            'trainer.quotes' => function ($query) use ($request) {
                $query->where('ticket_id', $request->id);
            }
        ])->findorfail($request->id);
        $primarySkills = PrimarySkill::getSkillByCourseCategory($ticketDetails->course_category_id)->toArray();
        $allCourseCategory = array_column(CourseCategory::getAllCourseCategory(), 'name', 'id');
        $getCourse = array_column(Course::getCourse($ticketDetails->course_category_id), 'name', 'id');
        $allCourseCategory[0] = 'Other';
        $getCourse[0] = 'Other';
        $ticketStatus = CommonHelper::getTicketStatus();
        $ticketStatus = $ticketStatus->filter(function ($value, $key) use ($ticketDetails) {
            if ($ticketDetails->status == config('constants.TICKET.INACTIVE')) {
                if (!empty($ticketDetails->trainer_id)) {
                    return $key >= config('constants.TICKET.IN_PROGRESS');
                } elseif (!empty($ticketDetails->user_id)) {
                    return $key >= config('constants.TICKET.PENDING');
                } else {
                    return $key >= config('constants.TICKET.NEW');
                }
            }
            return $key >= $ticketDetails->status;
        });

        return view('tickets.view', compact('page_title', 'ticketDetails', 'users', 'primarySkills', 'allCourseCategory', 'getCourse', 'ticketStatus'));
    }

    /**
     * Edit the all ticket details with fillable form
     * 
     * @param $request
     * @return form
     */
    public function edit(Request $request)
    {
        $page_title = 'Ticket view details';
        $users = User::getSubAdminList(true);
        $ticketDetails = Ticket::with([
            'user:id,first_name,last_name',
            'ticketType:id,name',
            'course:id,name',
            'primarySkills',
            'timezone:id,label',
            'customer:id,first_name,last_name,email',
            'ticketEmployees:id,ticket_id,employee_name,country_code,phone_number,email',
            'trainer:id,first_name,last_name',
            'trainer.quotes' => function ($query) use ($request) {
                $query->where('ticket_id', $request->id);
            }
        ])->findorfail($request->id);
        $primarySkills = PrimarySkill::getSkillByCourseCategory($ticketDetails->course_category_id)->toArray();
        $allCourseCategory = array_column(CourseCategory::getAllCourseCategory(), 'name', 'id');
        $getCourse = array_column(Course::getCourse($ticketDetails->course_category_id), 'name', 'id');
        $allCourseCategory[0] = 'Other';
        $getCourse[0] = 'Other';
        $ticketStatus = CommonHelper::getTicketStatus();
        $ticketStatus = $ticketStatus->filter(function ($value, $key) use ($ticketDetails) {
            if ($ticketDetails->status == config('constants.TICKET.INACTIVE')) {
                if (!empty($ticketDetails->trainer_id)) {
                    return $key >= config('constants.TICKET.IN_PROGRESS');
                } elseif (!empty($ticketDetails->user_id)) {
                    return $key >= config('constants.TICKET.PENDING');
                } else {
                    return $key >= config('constants.TICKET.NEW');
                }
            }
            return $key >= $ticketDetails->status;
        });
        // Session::put('TICKET_ID', $ticketDetails->ticket_id);
        // Session::put('TICKET_PK', $ticketDetails->id);
        // Ticket::setTicketMessage($ticketDetails);

        return view('tickets.edit', compact('page_title', 'ticketDetails', 'users', 'primarySkills', 'allCourseCategory', 'getCourse', 'ticketStatus'));
    }

    /**
     * Update the basic ticket info
     * 
     * @param $request collection
     * @param $id int
     * @return form
     */
    public function update(TicketRequest $request, $id)
    {
        try {
            $ticket = Ticket::findorfail($id);
            if (empty($request->is_global)) {
                $request->merge(['is_global' => 0]);
            }
            if ($request->status == config('constants.TICKET.IN_PROGRESS') && $ticket->trainer_id == null) {
                $request->session()->flash('error', __("Please assign the trainer."));

                return redirect()->route('tickets.view', ['id' => $id]);
            }
            if ($ticket->date != $request->date || $ticket->time != $request->time || $ticket->timezone_id != $request->timezone_id) {
                $ticketTimestamp = $this->checkDateTime($request);
                if (empty($ticketTimestamp)) {
                    return redirect()->route('tickets.view', ['id' => $request->id]);
                }
                $request->merge(['ticket_timestamp' => $ticketTimestamp]);
            }
            if (empty($ticket->user_id) && !empty($request->user_id)) {
                $request->merge(['status' => config('constants.TICKET.PENDING')]);
            }
            \DB::beginTransaction();

            $oldStatus = $ticket->status;
            if ($ticket->update($request->except(['primary_skill', 'employees', 'dates', 'country_code']))) {
                Ticket::setTicketMessage($ticket);
                $ticket->savePrimarySkill($ticket, $request->primary_skill);
                $statusLabel = CommonHelper::getTicketStatus();
                // Save ticket log for status
                if ($oldStatus != $ticket->status) {
                    event(new TicketLog($ticket, \Auth::user()->full_name . ' change the ticket status ' . $statusLabel[$oldStatus] . ' to ' . $statusLabel[$ticket->status]));
                }
                // Save ticket log for update details
                event(new TicketLog($ticket, \Auth::user()->full_name . ' change the ticket details.'));
                // Send the notification for status chage
                $this->notificationForChangeStatus($ticket, $oldStatus);

                \DB::commit();
                $request->session()->flash('success', __("Ticket updated successfully"));

                return redirect()->route('tickets.view', ['id' => $id]);
            }
            \DB::rollBack();
            $request->session()->flash('error', __("Can not updated ticket, Please try again"));

            return redirect()->route('tickets.view', ['id' => $id]);
        } catch (\Exception $ex) {
            \DB::rollBack();
            $request->session()->flash('error', __("Can not updated ticket, Please try again"));

            return redirect()->route('tickets.view', ['id' => $id]);
        }
    }

    /**
     * Checkt the ticket date, time and timezone
     * 
     * @param $request form submit data
     * @return bool and timestamp
     */
    public function checkDateTime($request)
    {
        $isError = 0;
        $dateTime = $request->date . ' ' . $request->time;
        $timezone = Timezone::getTimezoneById($request->timezone_id);
        $ticketUtcTime = CommonHelper::getUtcTime($dateTime, $timezone->timezone);
        $currentTime = CommonHelper::convertDateToSpecificTimezone(Carbon::now(), $timezone->timezone);
        $hourFlag = '';
        if (in_array($request->ticket_type_id, [1, 2])) {
            $currentTime->addHours(config('constants.JOB_TRAINING_TICKET.CREATION_LIMIT'));
            $hourFlag = config('constants.JOB_TRAINING_TICKET.CREATION_LIMIT');
        } else if ($request->ticket_type_id == 3) {
            $currentTime->addHours(config('constants.INTERVIEW_SUPPORT_TICKET.CREATION_LIMIT'));
            $hourFlag = config('constants.INTERVIEW_SUPPORT_TICKET.CREATION_LIMIT');
        }
        if ($dateTime < $currentTime->toDateTimeString()) {
            $request->session()->flash('error', __("Date and time must be after $hourFlag hours"));

            return $isError;
        }
        if ($request->ticket_type_id == 3) {
            $ticketTime = CommonHelper::convertDateToSpecificTimezone($ticketUtcTime, 'Asia/kolkata');
            $today = Carbon::today('Asia/kolkata')->addHours(config('constants.INTERVIEW_SUPPORT_TICKET.CURRENT_DAY_END_TIME'));

            if ($ticketTime->toDateString() == $today->toDateString() && $ticketTime >= $today) {
                $request->session()->flash('error', __("This type of ticket must be before " . config('constants.INTERVIEW_SUPPORT_TICKET.CURRENT_DAY_END_TIME_LABEL') . " IST"));

                return $isError;
            }
            if ($ticketTime->toTimeString() < config('constants.INTERVIEW_SUPPORT_TICKET.START_TIME') || $ticketTime->toTimeString() > config('constants.INTERVIEW_SUPPORT_TICKET.END_TIME')) {
                $request->session()->flash('error', __("This type of ticket must be between " . config('constants.INTERVIEW_SUPPORT_TICKET.START_TIME_LABEL') . " to " . config('constants.INTERVIEW_SUPPORT_TICKET.END_TIME_LABEL') . " IST"));

                return $isError;
            }
        }

        return $ticketUtcTime->timestamp;
    }

    /**
     * Send the notification based on change status
     * 
     * @param $ticket object full ticket data
     * @param $oldStatus integer ticket old status
     */
    public function notificationForChangeStatus($ticket, $oldStatus)
    {
        if (!empty($ticket->trainer_id)) {
            $updateDetailTrainer = [
                'sender_id' => \Auth::user()->id,
                'receiver_id' => $ticket->trainer_id,
                'sender_type' => config('constants.NOTIFICATION_TYPE.USER'),
                'receiver_type' => config('constants.NOTIFICATION_TYPE.TRAINER'),
                'title' => 'Ticket updated',
                'message' => 'Ticket detail is updated by admin',
                'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.TICKET'),
                'push_notification' => 1
            ];
            // Send the notification to trainer
            event(new SendNotification($ticket, $updateDetailTrainer));
        }
        $updateDetailCustomer = [
            'sender_id' => \Auth::user()->id,
            'receiver_id' => $ticket->customer_id,
            'sender_type' => config('constants.NOTIFICATION_TYPE.USER'),
            'receiver_type' => config('constants.NOTIFICATION_TYPE.CUSTOMER'),
            'title' => 'Ticket updated',
            'message' => 'Ticket detail is updated by admin',
            'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.TICKET'),
            'push_notification' => 1
        ];
        event(new SendNotification($ticket, $updateDetailCustomer));

        if ($ticket->status != $oldStatus) {
            if (!empty($ticket->trainer_id)) {
                $notificationDataTrainer = [
                    'sender_id' => \Auth::user()->id,
                    'receiver_id' => $ticket->trainer_id,
                    'sender_type' => config('constants.NOTIFICATION_TYPE.USER'),
                    'receiver_type' => config('constants.NOTIFICATION_TYPE.TRAINER'),
                    'title' => 'Ticket status change',
                    'message' => 'Ticket status has been updated to ' . $ticket->status_label,
                    'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.TICKET'),
                    'push_notification' => 1
                ];
                // Send the notification to trainer
                event(new SendNotification($ticket, $notificationDataTrainer));
            }

            $notificationDataCustomer = [
                'sender_id' => \Auth::user()->id,
                'receiver_id' => $ticket->customer_id,
                'sender_type' => config('constants.NOTIFICATION_TYPE.USER'),
                'receiver_type' => config('constants.NOTIFICATION_TYPE.CUSTOMER'),
                'title' => 'Ticket status change',
                'message' => 'Ticket status has been updated to ' . $ticket->status_label,
                'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.TICKET'),
                'push_notification' => 1
            ];

            // Send the notification to customer
            event(new SendNotification($ticket, $notificationDataCustomer));
        }

        // send notification to sub admin when any changes is made super admin
        if (!empty($ticket) && $ticket->user_id != \Auth::user()->id) {
            $notificationSubadmin = [
                'sender_id' => \Auth::user()->id,
                'receiver_id' => $ticket->user_id,
                'sender_type' => config('constants.NOTIFICATION_TYPE.USER'),
                'receiver_type' => config('constants.NOTIFICATION_TYPE.USER'),
                'title' => 'Ticket updated',
                'message' => 'Ticket detail is updated by ' . \Auth::user()->full_name,
                'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.TICKET'),
                'push_notification' => 0
            ];
            event(new SendNotification($ticket, $notificationSubadmin));
        }
    }

    /**
     * Update the ticket as global
     * update the YES/NO value
     * 
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function updateIsGlobal(Request $request)
    {
        if ($request->ajax()) {
            $ticket = Ticket::findorfail($request->id);
            $ticket->is_global = ($request->is_global == '0') ? config('constants.TICKET.IS_GLOBAL_YES') : config('constants.TICKET.IS_GLOBAL_NO');
            if ($ticket->save()) {
                return true;
            }
            return false;
        }

        return false;
    }
}
