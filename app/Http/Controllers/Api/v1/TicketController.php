<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\TicketRequest;
use App\Models\Ticket;
use App\Models\Timezone;
use Auth;
use Carbon\Carbon;
use CommonHelper;
use Illuminate\Http\Request;
use App\Events\SendNotification;
use App\Events\TicketLog;

class TicketController extends ApiController
{
    /**
     * URL - {{local}}/v1/ticket-list?limit=10
     * METHOD - GET
     * Get paginated tickets
     * Filter and search the data
     * 
     * @param \Illuminate\Http\Request $request
     * @return object|JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('limit', 10);
            $customerId = $request->get('customer_id') ?? '';
            $trainerId = $request->get('trainer_id') ?? '';
            if (Auth::guard('trainer')->check()) {
                $loggedinId = Auth::guard('trainer')->user()->id;
                $userDetail = 'customer:id,first_name,last_name,avatar';
                $userCondition = ['trainer_id', '=', $loggedinId];
                $senderType = [config('constants.SENDER_TYPE.USER'), config('constants.SENDER_TYPE.CUSTOMER')];
                $meetingType = ['trainer_is_read', '=', config('constants.UNREAD_NOTIFICATION')];
            } else {
                $loggedinId = Auth::guard('customer')->user()->id;
                $userDetail = 'trainer:id,first_name,last_name,avatar';
                $userCondition = ['customer_id', '=', $loggedinId];
                $senderType = [config('constants.SENDER_TYPE.USER'), config('constants.SENDER_TYPE.TRAINER')];
                $meetingType = ['customer_is_read', '=', config('constants.UNREAD_NOTIFICATION')];
            }
            $tickets = Ticket::select(
                'id',
                'customer_id',
                'timezone_id',
                'trainer_id',
                'course_category_id',
                'course_id',
                'ticket_type_id',
                'ticket_id',
                'other_course_category',
                'other_course',
                'other_primary_skill',
                'status',
                'created_at',
                'date',
                'time',
                'request_ticket_close',
            )
                ->withCount([
                    'trainerInvoices as has_created_invoice' => function ($query) use ($loggedinId) {
                        $query->where('trainer_id', $loggedinId);
                    },
                    'trainerInvoices as trainer_has_payment_update' => function ($query) use ($loggedinId) {
                        $query->where([['trainer_id', $loggedinId], ['is_read', config('constants.PAYMENT.UNREAD_NOTIFICATION')]]);
                    },
                    'customerInstallments as customer_has_payment_update' => function ($query) use ($loggedinId) {
                        $query->where([['customer_id', $loggedinId], ['is_read', config('constants.PAYMENT.UNREAD_NOTIFICATION')]]);
                    }
                ])
                ->withCount(['chatRooms as customer_trainer_chat' => function ($query) use ($userCondition) {
                    $query->where([
                        ['customer_id', '<>', NULL],
                        $userCondition
                    ]);
                }])
                ->withCount(['meetings as unread_meetings' => function ($query) use ($userCondition, $meetingType) {
                    $query->where([
                        $meetingType,
                        $userCondition
                    ]);
                }])
                ->with(['chatRooms' => function ($query) use ($userCondition, $senderType) {
                    $query->select(
                        "id",
                        "ticket_id",
                        "user_id",
                        "trainer_id",
                        "customer_id"
                    )
                        ->where([$userCondition])
                        ->withCount(['chatMessages as read_chat_message' => function ($q) use ($senderType) {
                            $q->whereIn('sender_type', $senderType)
                                ->where('is_read', config('constants.UNREAD_NOTIFICATION'));
                        }]);
                }])
                ->with([
                    'ticketType:id,name',
                    'course:id,name,cover_image',
                    'courseCategory:id,name',
                    'timezone:id,label,timezone,abbreviation',
                    'customer:id,first_name,last_name,avatar',
                    'customerQuote:id,ticket_id,quote,currency',
                    'trainer:id,first_name,last_name,avatar',
                    'trainerQuote' => function ($query) use ($loggedinId) {
                        $query->select(['id', 'ticket_id', 'trainer_id', 'quote', 'currency', 'payment_status'])
                            ->where('trainer_id', $loggedinId);
                    },
                ])
                ->where([$userCondition]);

            if (Auth::guard('trainer')->check()) {
                $tickets = $tickets->orderBy('assigned_trainer_date', 'desc');
            } else {
                $tickets = $tickets->orderBy('created_at', 'desc');
            }
            $tickets = $tickets->paginate($perPage);

            return $this->apiResponse->respondWithMessageAndPayload($tickets, __("Record fetched successfully"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Create ticket
     * URL - /v1/tickets/create
     * 
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function create(TicketRequest $request)
    {
        try {
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
                return $this->apiResponse->respondWithError(__("Date and time must be after $hourFlag hours"));
            }
            if ($request->ticket_type_id == 3) {
                $ticketTime = CommonHelper::convertDateToSpecificTimezone($ticketUtcTime, 'Asia/kolkata');
                $today = Carbon::today('Asia/kolkata')->addHours(config('constants.INTERVIEW_SUPPORT_TICKET.CURRENT_DAY_END_TIME'));

                if ($ticketTime->toDateString() == $today->toDateString() && $ticketTime >= $today) {
                    return $this->apiResponse->respondWithError(__("This type of ticket must be before " . config('constants.INTERVIEW_SUPPORT_TICKET.CURRENT_DAY_END_TIME_LABEL') . " IST"));
                }
                if ($ticketTime->toTimeString() < config('constants.INTERVIEW_SUPPORT_TICKET.START_TIME') || $ticketTime->toTimeString() > config('constants.INTERVIEW_SUPPORT_TICKET.END_TIME')) {
                    return $this->apiResponse->respondWithError(__("This type of ticket must be between " . config('constants.INTERVIEW_SUPPORT_TICKET.START_TIME_LABEL') . " to " . config('constants.INTERVIEW_SUPPORT_TICKET.END_TIME_LABEL') . " IST"));
                }
            }

            \DB::beginTransaction();
            $ticketId = CommonHelper::generateTicketId($request->ticket_type_id);
            $request->merge(['ticket_id' => $ticketId, 'ticket_timestamp' => $ticketUtcTime->timestamp]);
            $ticket = Ticket::create($request->except(['primary_skill', 'employees']));
            if ($ticket) {
                $ticket->savePrimarySkill($ticket, $request->primary_skill);
                if (isset($request->is_for_employee) && $request->is_for_employee == 1) {
                    $ticket->saveTicketEmployees($ticket, $request->employees);
                }
                \DB::commit();
                // Send the notification
                $notificationData = [
                    'sender_id' => $ticket->customer_id,
                    'receiver_id' => config('constants.ADMIN_ROLE.SUPER_ADMIN'),
                    'sender_type' => config('constants.NOTIFICATION_TYPE.CUSTOMER'),
                    'receiver_type' => config('constants.NOTIFICATION_TYPE.USER'),
                    'title' => 'New ticket generate',
                    'message' => 'New ticket ' . $ticket->ticket_id . ' has been created.',
                    'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.TICKET'),
                ];
                event(new SendNotification($ticket, $notificationData));
                event(new TicketLog($ticket, 'Ticket id ' . $ticket->ticket_id . ' has been created.'));
                return $this->apiResponse->respondWithMessage(__("Ticket created successfully"));
            }

            \DB::rollBack();
            return $this->apiResponse->respondWithError(__("Can not create ticket, Please try again"));
        } catch (\Exception $ex) {
            \DB::rollBack();
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * URL - {{local}}/v1/ticket-details/{id}
     * METHOD - GET
     * 
     * @param \Illuminate\Http\Request $request
     * @return object|JsonResponse
     */
    public function getTicket(Request $request)
    {
        try {
            $ticket = Ticket::select('id', 'user_id', 'customer_id', 'trainer_id', 'course_category_id', 'course_id', 'timezone_id', 'ticket_type_id', 'ticket_id', 'other_course_category', 'other_course', 'other_primary_skill', 'status', 'created_at', 'message', 'date', 'time', 'request_ticket_close')
                ->with([
                    'ticketType:id,name',
                    'timezone:id,timezone,label,abbreviation',
                    'course:id,name,cover_image',
                    'courseCategory:id,name',
                    'customer:id,first_name,last_name,avatar',
                    'customerQuote:id,ticket_id,quote,currency',
                    'customerQuote.installments' => function ($query) {
                        $query->where('payment_status', config('constants.PAYMENT.DUE'))->orderBy('created_at', 'asc')->limit(1);
                    },
                    'trainer:id,first_name,last_name,avatar',
                    'trainer.quotes' => function ($query) use ($request) {
                        $query->select(['id', 'ticket_id', 'trainer_id', 'quote', 'currency', 'payment_status'])
                            ->where('ticket_id', $request->id);
                    },
                    'user:id,first_name,last_name,country_code,phone_number,avatar',
                ])->findOrFail($request->id);

            return $this->apiResponse->respondWithMessageAndPayload($ticket, __("Record fetched successfully"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Send the request to admin for close the ticket
     * URL - /v1/trainer/mark-ticket-close/{id}
     * 
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function markTicketClose($id)
    {
        try {
            $ticket = Ticket::findorfail($id);

            if ($ticket->update([
                'request_ticket_close' => config('constants.TICKET.REQUEST_TICKET_CLOSE')
            ])) {
                // Send the notification to subadmin
                if (config('constants.ADMIN_ROLE.SUPER_ADMIN') != $ticket->user_id) {
                    $notificationSubAdminData = [
                        'sender_id' => Auth::guard('trainer')->user()->id,
                        'receiver_id' => $ticket->user_id,
                        'sender_type' => config('constants.NOTIFICATION_TYPE.TRAINER'),
                        'receiver_type' => config('constants.NOTIFICATION_TYPE.USER'),
                        'title' => 'Request for ticket close',
                        'message' => Auth::guard('trainer')->user()->full_name . ' is request to close the ticket id ' . $ticket->ticket_id,
                        'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.TICKET'),
                    ];
                    event(new SendNotification($ticket, $notificationSubAdminData));
                }
                // Send the notification to super admin
                $notificationSuperAdminData = [
                    'sender_id' => Auth::guard('trainer')->user()->id,
                    'receiver_id' => config('constants.ADMIN_ROLE.SUPER_ADMIN'),
                    'sender_type' => config('constants.NOTIFICATION_TYPE.TRAINER'),
                    'receiver_type' => config('constants.NOTIFICATION_TYPE.USER'),
                    'title' => 'Request for ticket close',
                    'message' => Auth::guard('trainer')->user()->full_name . ' is request to close the ticket id ' . $ticket->ticket_id,
                    'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.TICKET'),
                ];
                event(new SendNotification($ticket, $notificationSuperAdminData));
                // Add ticket log
                event(new TicketLog($ticket, Auth::guard('trainer')->user()->full_name . ' is request to close the ticket id ' . $ticket->ticket_id));

                return $this->apiResponse->respondWithMessageAndPayload(['request_ticket_close' => $ticket->request_ticket_close], __("Ticket close request has been send successfully."));
            }

            return $this->apiResponse->respondWithError(__("Can not send ticket close request, Please try again"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }
}
