<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\InterestedTicketsRequest;
use App\Models\Trainer;
use App\Events\TicketLog;
use App\Events\SendNotification;

class GlobalRequestController extends ApiController
{
    /**
     * URL - {{local}}/v1/global-request-list?limit=10
     * METHOD - GET
     * Get paginated global request for all tickets
     * Filter and search the data
     * 
     * @param \Illuminate\Http\Request $request
     * @return object|JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('limit', 10);
            $ticketType = $request->get('ticket_type_id') ?? '';
            $courseCategory = $request->get('course_category_id') ?? '';
            $courseId = $request->get('course_id') ?? '';
            $primarySkills = $request->get('primary_skill') ?? '';
            $postedBy = $request->get('posted_by') ?? '';
            $courseName = $request->get('course_name') ?? '';

            $tickets = Ticket::select(
                'id',
                'customer_id',
                'trainer_id',
                'course_category_id',
                'course_id',
                'ticket_type_id',
                'ticket_id',
                'status',
                'other_course_category',
                'other_course',
                'other_primary_skill',
                'created_at',
                'is_global'
            )
                ->with([
                    'ticketType:id,name',
                    'trainer:id,first_name,last_name,avatar',
                    'course:id,name,cover_image',
                    'courseCategory:id,name',
                    'customer:id,first_name,last_name,avatar',
                    'primarySkills:course_category_id,name,status'
                ])
                ->withCount(['interestedTrainers as has_shown_interest' => function ($query) {
                    $query->where('trainer_id', Auth::guard('trainer')->user()->id);
                }])
                ->whereDoesntHave('interestedTrainers', function ($q) {
                    $q->where('trainer_id', Auth::guard('trainer')->user()->id);
                })
                ->whereNull('tickets.trainer_id')
                ->where('is_global', config('constants.TICKET.IS_GLOBAL_YES'))
                ->whereNOTIn('tickets.status', [config('constants.TICKET.COMPLETE'), config('constants.TICKET.INACTIVE'), config('constants.TICKET.CANCEL')])
                ->orderBy('tickets.created_at', 'desc');

            if (!empty($ticketType)) {
                $ticketTypeVal = explode(',', $ticketType);
                $tickets->whereIn('tickets.ticket_type_id', $ticketTypeVal);
            }
            if (!empty($courseCategory)) {
                $courseCategoryVal = explode(',', $courseCategory);
                $tickets->whereIn('tickets.course_category_id', $courseCategoryVal);
            }
            if (!empty($courseId)) {
                $courseIdVal = explode(',', $courseId);
                $tickets->whereIn('tickets.course_id', $courseIdVal);
            }
            if (!empty($primarySkills)) {
                $primarySkillVal = explode(',', $primarySkills);
                $tickets->whereHas('primarySkills', function ($q) use ($primarySkillVal) {
                    $q->whereIn('primary_skills.id', $primarySkillVal);
                });
            }
            if (!empty($courseName)) {
                $tickets->where('tickets.other_course', 'LIKE', '%' . $courseName . '%')->orWhereHas('course', function ($q) use ($courseName) {
                    $q->where('courses.name', 'LIKE', '%' . $courseName . '%');
                });
            }

            if (!empty($postedBy)) {
                $date = \Carbon\Carbon::today()->subDays($postedBy);
                $tickets->where('tickets.created_at', '>=', $date);
            }
            $ticketData = $tickets->paginate($perPage);
            $loggedinUser = collect(['logged_in_user_status' => Auth::guard('trainer')->user()->status]);
            $response = $loggedinUser->merge($ticketData);

            return $this->apiResponse->respondWithMessageAndPayload($response);
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * URL - {{local}}/v1/mark-interested-ticket
     * METHOD - POST
     * Mark the ticket as interested
     * 
     * @param \Illuminate\Http\Request $request
     * @return object|JsonResponse
     */
    public function markInterestedTickets(InterestedTicketsRequest $request)
    {
        try {
            $tickets = Ticket::findorfail($request->ticket_id);
            if (empty($tickets->trainer_id)) {
                if (!in_array($tickets->status, [config('constants.TICKET.COMPLETE'), config('constants.TICKET.INACTIVE'), config('constants.TICKET.CANCEL')])) {
                    if ($tickets->interestedTrainers()->sync([Auth::guard('trainer')->user()->id], false)) {
                        // Save ticket log
                        $message = Auth::guard('trainer')->user()->full_name . ' mark the ticker id ' . $tickets->ticket_id . ' as interested';
                        event(new TicketLog($tickets, $message));
                        // send notification to sub admin or super admin
                        $notificationSubadmin = [
                            'sender_id' => \Auth::guard('trainer')->user()->id,
                            'receiver_id' => ($tickets->user_id) ? $tickets->user_id : config('constants.ADMIN_ROLE.SUPER_ADMIN'),
                            'sender_type' => config('constants.NOTIFICATION_TYPE.TRAINER'),
                            'receiver_type' => config('constants.NOTIFICATION_TYPE.USER'),
                            'title' => 'Trainer has marked ticket as interested',
                            'message' => $message,
                            'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.INTERESTED_TICKET'),
                            'push_notification' => 0
                        ];
                        event(new SendNotification($tickets, $notificationSubadmin));

                        return $this->apiResponse->respondWithMessage(__("Ticket is successfully mark as interested."));
                    }
                }
            }
            $message = in_array($tickets->status, [config('constants.TICKET.COMPLETE'), config('constants.TICKET.INACTIVE'), config('constants.TICKET.CANCEL')]) ? strtolower($tickets->status_label) : 'already assigned';

            return $this->apiResponse->respondWithError(__("You cannot send interest as ticket is " . $message), '', $tickets);
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * URL {{local}}/v1/interested-ticket-list?limit=10
     * METHOD - GET
     * Get all trainer mark interested records
     * 
     * @param \Illuminate\Http\Request $request
     * @return object|JsonResponse
     */
    public function interestedTicketList(Request $request)
    {
        try {
            $perPage = $request->get('limit', 10);

            $trainer = Trainer::select('trainers.id', 'trainers.first_name', 'trainers.last_name')
                ->with(['interestedTickets.chatRooms' => function ($query) {
                    $query->select(
                        "id",
                        "ticket_id",
                        "user_id",
                        "trainer_id",
                        "customer_id"
                    )->where([
                        ['user_id', '<>', NULL],
                        ['trainer_id', '=', Auth::guard('trainer')->user()->id]
                    ])
                        ->withCount(['chatMessages as read_chat_message' => function ($q) {
                            $q->whereIn('sender_type', [config('constants.SENDER_TYPE.USER')])
                                ->where('is_read', config('constants.UNREAD_NOTIFICATION'));
                        }]);
                }])
                ->with([
                    'interestedTickets' => function ($q) {
                        $q->select('tickets.id', 'tickets.trainer_id', 'tickets.user_id', 'tickets.customer_id',
                            'tickets.course_category_id', 'tickets.course_id', 'tickets.ticket_type_id', 'tickets.message',
                            'tickets.status', 'tickets.other_course_category', 'tickets.other_course', 'tickets.other_primary_skill',
                            'tickets.created_at', 'tickets.date', 'tickets.ticket_id')
                            ->orderBy('interested_ticket_trainer.created_at', 'DESC')
                            ->withCount(['meetings as unread_meetings' => function ($query) {
                                $query->where([
                                    ['trainer_is_read', '=', config('constants.UNREAD_NOTIFICATION')],
                                    ['trainer_id', '=', Auth::guard('trainer')->user()->id]
                                ]);
                            }]);
                    },
                    'interestedTickets.course:id,name,cover_image',
                    'interestedTickets.courseCategory:id,name',
                    'interestedTickets.ticketType:id,name',
                    'interestedTickets.customer:id,first_name,last_name,avatar',
                    'interestedTickets.trainerQuote' => function ($query) {
                        $query->select(['id', 'ticket_id', 'trainer_id', 'quote', 'currency', 'payment_status'])
                            ->where('trainer_id', Auth::guard('trainer')->user()->id);
                    },
                ])->where('id', Auth::guard('trainer')->user()->id);

            return $this->apiResponse->respondWithMessageAndPayload($trainer->paginate($perPage));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }
}
