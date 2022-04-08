<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProposalRequest;
use App\Models\Proposal;
use App\Models\Trainer;
use App\Models\Ticket;
use App\Events\SendNotification;
use App\Events\TicketLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use CommonHelper;

class ProposalController extends Controller
{
    /**
     * Get Ticket listing
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $page_title = 'Proposal Listing';

        return view('proposals.index', compact('page_title'));
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
            'name',
            'quote',
        ];

        //Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $order_arr[0]['column']; // Column index
        $columnName = !empty($tableColumn[$columnIndex]) ? $tableColumn[$columnIndex] : 'meetings.created_at'; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc

        $records = Proposal::where('ticket_id', '=', $request->id)
            ->with(['trainers' => function ($query) {
                $query->where('action', '=', config('constants.PROPOSAL.ACCEPTED'))->orderBy('updated_at', 'desc');
            }, 'trainers.trainer:id,first_name,last_name,avatar'])->orderBy($columnName, $columnSortOrder);

        $currentPage = $start / $rowperpage + 1;
        $records = $records->paginate($rowperpage, ["*"], "page", $currentPage);

        return response()->view(
            'proposals.get-list',
            compact(['records', 'rowperpage', 'currentPage', 'draw'])
        );
    }

    /**
     * Get meetings records with pagination, sorting, searching
     * 
     * @param \Illuminate\Http\Reuest $request
     * @return json
     */
    public function getTrainerList(Request $request)
    {
        //Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $ticket = Ticket::select('tickets.id', 'tickets.trainer_id', 'tickets.ticket_id', 'tickets.ticket_type_id')
            ->findOrFail($request->route('id'));
        $records = Trainer::active()->orderBy('created_at', 'desc');

        if (!empty($request->interested_trainer)) {
            $records = $ticket->interestedTrainers()->active()
                ->select(['trainers.id', 'trainers.first_name', 'trainers.last_name', 'trainers.email', 'trainers.course_category_id', 'trainers.skill_title', 'trainers.total_experience_year', 'trainers.total_experience_month', 'trainers.training_price', 'trainers.job_support_price', 'trainers.interview_support_price'])
                ->orderBy('interested_ticket_trainer.created_at', 'desc');
        }
        $records->with(['primarySkills', 'courseCategory']);
        if (!empty($request->recommended_trainer)) {
            $ticketSkills = array_column(\DB::table('primary_skill_ticket')->select('primary_skill_id')->where('ticket_id', $request->route('id'))->get()->toArray(), 'primary_skill_id');
            $records->whereHas('primarySkills', function ($q) use ($ticketSkills) {
                $q->whereIn('primary_skill_id', $ticketSkills);
            });
        }

        if (!empty($request->search)) {
            $records->where(function ($query) use ($request) {
                $query->where(\DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', '%' . $request->search . '%')
                    ->orWhereHas('primarySkills', function ($q) use ($request) {
                        $q->where('name', 'LIKE', '%' . $request->search . '%');
                    })->orWhereHas('courseCategory', function ($q) use ($request) {
                        $q->where('name', 'LIKE', '%' . $request->search . '%');
                    })->orWhere('skill_title', 'LIKE', '%' . $request->search . '%');
            });
        }

        $currentPage = $start / $rowperpage + 1;
        $records = $records->paginate($rowperpage, ["*"], "page", $currentPage);

        return response()->view(
            'proposals.get-trainer-list',
            compact(['records', 'rowperpage', 'currentPage', 'draw', 'ticket'])
        );
    }

    /**
     * Create the specified resourse.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $page_title = 'Create New Proposal';
        $ticket = Ticket::with(['customerQuote'])->findOrFail($request->id);
        if (!$ticket->customerQuote) {
            $request->session()->flash('error', 'Please add customer pricing first.');

            return redirect()->route('proposals', ['id' => $request->id]);
        }

        return view('proposals.create', compact('page_title'));
    }

    /**
     * Store the specified resourse.
     *
     * @param \App\Http\Requests\ProposalRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProposalRequest $request)
    {
        try {
            if (!$this->validateProposalQuote($request)) {
                $request->session()->flash('error', 'Quote must be less than customer final price.');

                return back();
            }
            $request->merge(['user_id' => \Auth::id()]);
            $proposal = Proposal::create($request->except(['select_all', 'search', 'ids', 'recommended_trainer', 'interested_trainer']));
            if ($proposal) {
                $proposal->trainers()->createMany($request->ids);
                $request->session()->flash('success', 'Proposal created successfully.');
                // Save ticket log
                event(new TicketLog($proposal, 'New proposal ' . $proposal->name . ' has been created by ' . \Auth::user()->full_name));
                $trainerIds = array_column($request->ids, 'trainer_id');
                foreach ($trainerIds as $key => $value) {
                    $notificationDataTrainer = [
                        'sender_id' => \Auth::user()->id,
                        'receiver_id' => $value,
                        'sender_type' => config('constants.NOTIFICATION_TYPE.USER'),
                        'receiver_type' => config('constants.NOTIFICATION_TYPE.TRAINER'),
                        'title' => 'New Proposal Received',
                        'message' => 'You have got a New proposal request for Training from Desi trainer team.',
                        'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.PROPOSAL'),
                        'push_notification' => 1,
                        'proposal_name' => $proposal->name ?? ''
                    ];
                    event(new SendNotification($proposal, $notificationDataTrainer));
                }

                return redirect()->route('proposals', ['id' => $request->id]);
            }
            $request->session()->flash('error', 'Proposal can not be created, Please try again.');

            return redirect()->route('proposals', ['id' => $request->id]);
        } catch (\Exception $ex) {
            $request->session()->flash('error', 'Something went wrong, Please try again.');

            return redirect()->route('proposals', ['id' => $request->id]);
        }
    }

    /**
     * View details of specific resourse
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function view(Request $request)
    {
        try {
            $page_title = 'View Proposal';
            $ticket = Ticket::withCount(['customerInstallments as customer_has_made_payment' => function ($q) {
                $q->where('payment_status', config('constants.PAYMENT.PAID'));
            }])->findOrFail($request->id);
            $proposal = Proposal::where([['id', $request->proposal_id], ['ticket_id', $request->id]])->first();
            if ($proposal) {
                $proposalTrainers = $proposal->trainers()->with([
                    'trainer:id,first_name,last_name,avatar,skill_title,course_category_id,total_experience_year,total_experience_month',
                    'trainer.primarySkills',
                    'trainer.courseCategory'
                ])->whereHas('trainer', function ($q) {
                    $q->whereNull('deleted_at');
                });
                if ($request->trainer_status != '') {
                    $proposalTrainers->where('action', $request->trainer_status);
                }
                $proposalTrainers = $proposalTrainers->paginate(10);

                if ($request->ajax()) {
                    return view('proposals.view-trainer-list', compact('proposal', 'proposalTrainers', 'ticket'));
                } else {
                    return view('proposals.view', compact('proposal', 'proposalTrainers', 'ticket', 'page_title'));
                }
            }
            $request->session()->flash('error', 'Something went wrong, Please try again.');

            return redirect()->route('proposals', ['id' => $request->id]);
        } catch (\Exception $ex) {
            $request->session()->flash('error', 'Something went wrong, Please try again.');

            return redirect()->route('proposals', ['id' => $request->id]);
        }
    }

    /**
     * Assign trainer
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function assignTrainer(Request $request)
    {
        try {
            $ticket = Ticket::withCount(['customerInstallments as customer_has_made_payment' => function ($q) {
                $q->where('payment_status', config('constants.PAYMENT.PAID'));
            }])->findOrFail($request->id);
            $proposal = Proposal::where([['id', $request->proposal_id], ['ticket_id', $request->id]])->firstOrFail();
            $trainer = $proposal->trainers()->where('trainer_id', $request->trainer_id)->firstOrFail();
            if ($trainer->action != config('constants.PROPOSAL.ACCEPTED')) {
                $request->session()->flash('error', 'This trainer has not accepted the proposal.');

                return redirect()->route('tickets.proposals.view', ['id' => $request->id, 'proposal_id' => $proposal->id]);
            }
            if ($proposal->is_assigned == 1) {
                $request->session()->flash('error', 'This proposal is already assigned, Please create a new one.');

                return redirect()->route('proposals', ['id' => $request->id]);
            }
            if ($ticket->customer_has_made_payment == 0) {
                $request->session()->flash('error', 'Customer has not made a payment yet.');

                return redirect()->route('tickets.proposals.view', ['id' => $request->id, 'proposal_id' => $proposal->id]);
            }
            $ticket = $proposal->ticket()->firstOrFail();
            $ticket->trainer_id = $request->trainer_id;
            $ticket->assigned_trainer_date = Carbon::now();
            $ticket->status = config('constants.TICKET.IN_PROGRESS');
            if ($ticket->save()) {
                $proposal->is_assigned = 1;
                $proposal->save();
                $ticket->trainerQuote()->updateOrCreate(
                    ['trainer_id' => $request->trainer_id],
                    ['quote' => $proposal->quote, 'currency' => $proposal->currency]
                );
                $request->session()->flash('success', 'Trainer assigned successfully.');
                // send notification
                $notificationDataTrainer = [
                    'sender_id' => \Auth::user()->id,
                    'receiver_id' => $request->trainer_id,
                    'sender_type' => config('constants.NOTIFICATION_TYPE.USER'),
                    'receiver_type' => config('constants.NOTIFICATION_TYPE.TRAINER'),
                    'title' => 'Ticket assigned',
                    'message' => 'New ticket has been assigned. check the link now',
                    'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.TICKET'),
                    'push_notification' => 1
                ];
                $notificationDataCustomer = [
                    'sender_id' => \Auth::user()->id,
                    'receiver_id' => $ticket->customer_id,
                    'sender_type' => config('constants.NOTIFICATION_TYPE.USER'),
                    'receiver_type' => config('constants.NOTIFICATION_TYPE.CUSTOMER'),
                    'title' => 'Ticket are assigned to trainer',
                    'message' => 'Your ticket has been assigned to trainer. check the link now',
                    'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.TICKET'),
                    'push_notification' => 1
                ];
                // Send the notification to trainer
                event(new SendNotification($ticket, $notificationDataTrainer));
                // Send the notification to customer
                event(new SendNotification($ticket, $notificationDataCustomer));
                // Save ticket log
                event(new TicketLog($proposal, $ticket->trainer->full_name . ' trainer are assigned by ' . \Auth::user()->full_name));

                return redirect()->route('proposals', ['id' => $request->id]);
            }
        } catch (\Exception $ex) {
            $request->session()->flash('error', 'Something went wrong, Please try again.');

            return redirect()->route('proposals', ['id' => $request->id]);
        }
    }

    /**
     * Edit details of specific resourse
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        try {
            $page_title = 'Edit Proposal';
            $proposal = Proposal::withCount(['trainers' => function ($query) {
                $query->where(function ($q) {
                    $q->where('action', config('constants.PROPOSAL.ACCEPTED'))
                        ->orWhere('action', config('constants.PROPOSAL.DENIED'));
                });
            }])->where([['id', $request->proposal_id], ['ticket_id', $request->id]])->first();

            return view('proposals.edit', compact('proposal', 'page_title'));
        } catch (\Exception $ex) {
            $request->session()->flash('error', 'Something went wrong, Please try again.');

            return redirect()->route('proposals', ['id' => $request->id]);
        }
    }

    /**
     * Update the specified resourse.
     *
     * @param \App\Http\Requests\ProposalRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(ProposalRequest $request)
    {
        try {
            if (!$this->validateProposalQuote($request)) {
                $request->session()->flash('error', 'Quote must be less than customer final price.');

                return back();
            }
            $proposal = Proposal::withCount(['trainers' => function ($query) {
                $query->where(function ($q) {
                    $q->where('action', config('constants.PROPOSAL.ACCEPTED'))
                        ->orWhere('action', config('constants.PROPOSAL.DENIED'));
                });
            }])->findOrFail($request->proposal_id);
            if ($proposal->trainers_count > 0) {
                $requestData = $request->except(['currency', 'quote']);
            } else {
                $requestData = $request->all();
            }

            if ($proposal->update($requestData)) {
                $request->session()->flash('success', 'Proposal created successfully.');
                // Save ticket log
                event(new TicketLog($proposal, $proposal->name . ' proposal has been edit by ' . \Auth::user()->full_name));

                return redirect()->route('proposals', ['id' => $request->id]);
            }
            $request->session()->flash('error', 'Proposal can not be created, Please try again.');

            return redirect()->route('proposals', ['id' => $request->id]);
        } catch (\Exception $ex) {
            $request->session()->flash('error', 'Something went wrong, Please try again.');

            return redirect()->route('proposals', ['id' => $request->id]);
        }
    }

    /**
     * Delete specific resource
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        try {
            $proposal = Proposal::findOrFail($request->proposal_id);

            if ($proposal->delete()) {
                // Save ticket log
                event(new TicketLog($proposal, $proposal->name . ' proposal has been delete by ' . \Auth::user()->full_name));
                $request->session()->flash('success', 'Proposal deleted successfully.');

                return redirect()->route('proposals', ['id' => $request->id]);
            }
            $request->session()->flash('error', 'Proposal can not be deleted, Please try again.');

            return redirect()->route('proposals', ['id' => $request->id]);
        } catch (\Exception $ex) {
            $request->session()->flash('error', 'Something went wrong, Please try again.');

            return redirect()->route('proposals', ['id' => $request->id]);
        }
    }

    /**
     * Check proposal quote with customer pricing
     */
    public function checkProposalQuote(Request $request)
    {
        try {
            return ($this->validateProposalQuote($request)) ? 'true' : 'false';
        } catch (\Exception $ex) {
            return 'false';
        }
    }

    /**
     * Validate proposal quote with customer pricing
     */
    public function validateProposalQuote(Request $request)
    {
        try {
            $ticket = Ticket::with(['customerQuote'])->findOrFail($request->id);
            if ($ticket->customerQuote) {

                if ($request->currency == $ticket->customerQuote->currency && trim($request->quote) > $ticket->customerQuote->quote) {
                    return false;
                }
    
                if($request->currency != $ticket->customerQuote->currency) {
                    $quotePrice = CommonHelper::foreignExchangePrice($ticket->customerQuote->currency, $ticket->customerQuote->quote);

                    if ($quotePrice && trim($request->quote) > $quotePrice) {
                        return false;
                    }
                }

                return true;
            }

            return false;
        } catch (\Exception $ex) {
            return false;
        }
    }
}
