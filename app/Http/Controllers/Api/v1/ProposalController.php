<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\ProposalReactionRequest;
use App\Models\ProposalTrainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\TicketLog;
use App\Events\SendNotification;

class ProposalController extends ApiController
{

    /**
     * URL - {{local}}/v1/trainer/proposals?limit=10
     * METHOD - GET
     * Get the all proposals
     * 
     * @param \Illuminate\Http\Request $request
     * @return object|JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('limit', 10);
            $proposal = ProposalTrainer::select('id', 'proposal_id', 'trainer_id', 'action', 'denied_reason')
                ->with([
                    'proposal' => function ($query) {
                        $query->select('id', 'name', 'description', 'quote', 'currency', 'ticket_id', 'is_assigned')
                            ->orderBy('created_at', 'desc');
                    },
                    'proposal.ticket' => function ($query) {
                        $query->select(
                            'id',
                            'trainer_id',
                            'customer_id',
                            'course_id',
                            'course_category_id',
                            'ticket_type_id',
                            'ticket_id',
                            'status',
                            \DB::raw('(CASE WHEN trainer_id IS NULL THEN "" WHEN trainer_id = ' . Auth::guard('trainer')->user()->id . ' THEN "Assigned" ELSE "Assigned to other" END) as assigned_flag')
                        );
                    },
                    'proposal.ticket.customer:id,first_name,last_name,avatar',
                    'proposal.ticket.ticketType:id,name',
                    'proposal.ticket.courseCategory:id,name',
                    'proposal.ticket.course:id,name,cover_image'
                ])
                ->where('trainer_id', '=', Auth::guard('trainer')->user()->id)
                ->orderBy('created_at', 'desc');

            $proposal = $proposal->paginate($perPage);

            return $this->apiResponse->respondWithMessageAndPayload($proposal);
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * URL - {{local}}/v1/trainer/proposal-action/{id}
     * METHOD - POST
     * Action of the trainer on proposal either accepted or denied
     * 
     * @param \App\Http\Requests\ProposalReactionRequest $request
     * @return object|JsonResponse
     */
    public function proposalAction(ProposalReactionRequest $request)
    {
        try {
            $proposal = ProposalTrainer::where([['proposal_id', $request->id], ['trainer_id', Auth::guard('trainer')->user()->id]])->first();
            if (!$proposal) {
                return $this->apiResponse->respondWithError(__("Record not found!"));
            }
            $ticket = $proposal->proposal->ticket;
            if ($proposal->proposal->is_assigned == 1 || in_array($ticket->status, [config('constants.TICKET.COMPLETE'), config('constants.TICKET.INACTIVE'), config('constants.TICKET.CANCEL')])) {
                $proposalData = $proposal->load([
                    'proposal' => function ($query) {
                        $query->select('id', 'name', 'description', 'quote', 'currency', 'ticket_id', 'is_assigned');
                    },
                    'proposal.ticket' => function ($query) {
                        $query->select(
                            'id',
                            'user_id',
                            'trainer_id',
                            'customer_id',
                            'course_id',
                            'course_category_id',
                            'ticket_type_id',
                            'ticket_id',
                            'status',
                            \DB::raw('(CASE WHEN trainer_id IS NULL THEN "" WHEN trainer_id = ' . Auth::guard('trainer')->user()->id . ' THEN "Assigned" ELSE "Assigned to other" END) as assigned_flag')
                        );
                    },
                    'proposal.ticket.customer:id,first_name,last_name,avatar',
                    'proposal.ticket.ticketType:id,name',
                    'proposal.ticket.courseCategory:id,name',
                    'proposal.ticket.course:id,name,cover_image'
                ]);
                $message = in_array($ticket->status, [config('constants.TICKET.COMPLETE'), config('constants.TICKET.INACTIVE'), config('constants.TICKET.CANCEL')]) ? 'ticket is ' . strtolower($ticket->status_label) : 'proposal is already assigned';

                return $this->apiResponse->respondWithError(__("Can not perform action as this " . $message), null, $proposalData);
            }

            if ($proposal->update($request->only(['action', 'denied_reason']))) {
                if (config('constants.PROPOSAL.ACCEPTED') == $request->action) {
                    $ticket->interestedTrainers()->sync([Auth::guard('trainer')->user()->id], false);
                }
                $status = (config('constants.PROPOSAL.ACCEPTED') == $request->action) ? 'accept' : 'denied';
                // Save ticket log                
                $proposal->ticket_id = $proposal->proposal->ticket_id;
                $message = Auth::guard('trainer')->user()->full_name . ' ' . $status . ' the ' . $proposal->proposal->name . ' proposal';
                event(new TicketLog($proposal, $message));
                // send notification to sub admin
                if (config('constants.ADMIN_ROLE.SUPER_ADMIN') != $ticket->user_id) {
                    $notificationSubadmin = [
                        'sender_id' => \Auth::guard('trainer')->user()->id,
                        'receiver_id' => $ticket->user_id,
                        'sender_type' => config('constants.NOTIFICATION_TYPE.TRAINER'),
                        'receiver_type' => config('constants.NOTIFICATION_TYPE.USER'),
                        'title' => 'Proposal ' . $status,
                        'message' => $message,
                        'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.PROPOSAL_TRAINER'),
                        'push_notification' => 0
                    ];
                    event(new SendNotification($proposal, $notificationSubadmin));
                }
                // send notification to super admin
                $notificationSuperAdmin = [
                    'sender_id' => \Auth::guard('trainer')->user()->id,
                    'receiver_id' => config('constants.ADMIN_ROLE.SUPER_ADMIN'),
                    'sender_type' => config('constants.NOTIFICATION_TYPE.TRAINER'),
                    'receiver_type' => config('constants.NOTIFICATION_TYPE.USER'),
                    'title' => 'Proposal ' . $status,
                    'message' => $message,
                    'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.PROPOSAL_TRAINER'),
                    'push_notification' => 0
                ];
                event(new SendNotification($proposal, $notificationSuperAdmin));

                return $this->apiResponse->respondWithMessage();
            }

            return $this->apiResponse->respondWithError(__("Can not perform action, Please try again!"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }
}
