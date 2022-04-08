<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditInvoiceRequest;
use App\Models\Ticket;
use App\Models\CustomerQuoteInstallment;
use App\Models\TrainerQuoteInvoice;
use CommonHelper;
use Illuminate\Http\Request;
use App\Events\SendNotification;
use App\Events\TicketLog;
use Illuminate\Pagination\LengthAwarePaginator;

class InvoiceController extends Controller
{
    /**
     * Get Ticket listing
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $page_title = 'Invoice Listing';

        return view('invoices.index', compact('page_title'));
    }

    /**
     * Get meetings records with pagination, sorting, searching
     * 
     * @param \Illuminate\Http\Reuest $request
     * @return json
     */
    public function getList(Request $request)
    {
        //Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page
        $isCustomer = 0;

        $records = Ticket::select('id', 'customer_id', 'trainer_id')
            ->with([
                'customerQuote:id,customer_id,ticket_id,quote,currency',
                'customerQuote.installments',
                'trainer:id',
                'trainer.quotes' => function ($query) use ($request) {
                    $query->select(['id', 'ticket_id', 'trainer_id', 'quote', 'currency', 'payment_status'])
                        ->where('ticket_id', $request->id);
                },
                'trainer.quotes.invoices'
            ])->findOrFail($request->id);

        if (!empty($request->invoice_type) && $request->invoice_type == 1) {
            $isCustomer = 1;
            $records = ($records->customerQuote) ? $records->customerQuote->installments() : collect([]);
        } else {
            $records = ($records->trainer && $records->trainer->quotes->isNotEmpty()) ? $records->trainer->quotes->first()->invoices()->orderBy('created_at', 'desc') : collect([]);
        }
        $invoiceUser = ($isCustomer) ? 'customer' : 'trainer';
        $currentPage = $start / $rowperpage + 1;
        $records = ($records->count() > 0) ? $records->paginate($rowperpage, ["*"], "page", $currentPage) : new LengthAwarePaginator(collect([]), 0, 10, 1);

        return response()->view(
            "invoices.get-$invoiceUser-list",
            compact(['records', 'rowperpage', 'currentPage', 'draw'])
        );
    }

    /**
     * View the specified resourse.
     *
     * @return \Illuminate\Http\Response
     */
    public function view(Request $request)
    {
        $page_title = 'View Invoice';
        $invoice = CustomerQuoteInstallment::findOrFail($request->invoice_id);

        return view('invoices.view', compact('page_title', 'invoice'));
    }

    /**
     * Edit the specified resourse.
     *
     * @return \Illuminate\Http\Response
     */
    public function editTrainerInvoice(Request $request)
    {
        try {
            $invoice = TrainerQuoteInvoice::findOrFail($request->invoice_id);

            return view('invoices.edit-trainer-invoice', compact('invoice'));
        } catch (\Exception $ex) {
            $request->session()->flash('error', __('Something went wrong, Please try again!.'));

            return redirect()->route('tickets.invoices', ['id' => $request->id]);
        }
    }

    /**
     * Update the specified resourse.
     *
     * @param \App\Http\Requests\EditInvoiceRequest $request
     * @return \Illuminate\Http\Response
     */
    public function updateTrainerInvoice(EditInvoiceRequest $request)
    {
        try {
            $ticket = Ticket::findOrFail($request->id);
            if (in_array($ticket->status, [config('constants.TICKET.INACTIVE')])) {
                $request->session()->flash('error', "Can not perform any action as this ticket is inactive");

                return back();
            }
            $invoice = TrainerQuoteInvoice::findOrFail($request->invoice_id);
            $oldStatus = $invoice->payment_status;

            if ($invoice->update($request->only(['payment_status', 'file']))) {
                if ($oldStatus != $invoice->payment_status) {
                    $statusLabel = CommonHelper::getPaymentStatus();
                    event(new TicketLog($invoice->trainerQuote, \Auth::user()->full_name . ' has changed the trainer invoice status from ' . $statusLabel[$oldStatus] . ' to ' . $statusLabel[$invoice->payment_status]));

                    $notificationDataTrainer = [
                        'sender_id' => \Auth::user()->id,
                        'receiver_id' => $invoice->trainerQuote->trainer_id,
                        'sender_type' => config('constants.NOTIFICATION_TYPE.USER'),
                        'receiver_type' => config('constants.NOTIFICATION_TYPE.TRAINER'),
                        'title' => 'Invoice status changed',
                        'message' => 'Invoice status is changed. check the link now',
                        'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.PAYMENT'),
                        'push_notification' => 1
                    ];
                    event(new SendNotification($ticket, $notificationDataTrainer));
                }
                $request->session()->flash('success', __('Invoice details edited successfully.'));
            } else {
                $request->session()->flash('error', __('Invoice details not edited, Please try again!'));
            }

            return redirect()->route('tickets.invoices', ['id' => $request->id]);
        } catch (\Exception $ex) {
            $request->session()->flash('error', __('Something went wrong, Please try again!'));

            return redirect()->route('tickets.invoices', ['id' => $request->id]);
        }
    }
}
