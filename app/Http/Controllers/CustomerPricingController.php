<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerQuoteRequest;
use App\Http\Requests\InstallmentRequest;
use App\Models\Customer;
use App\Models\CustomerQuote;
use App\Models\CustomerQuoteInstallment;
use App\Models\Ticket;
use App\Events\TicketLog;
use App\Events\SendNotification;
use Carbon\Carbon;
use CommonHelper;
use Illuminate\Http\Request;

class CustomerPricingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $page_title = __('Customer Pricing');
            $ticketDetail = Ticket::select(['id', 'course_id', 'customer_id', 'ticket_type_id'])
                ->withCount(['customerInstallments as has_paid_installment' => function ($q) {
                    $q->where('payment_status', config('constants.PAYMENT.PAID'));
                }])
                ->with([
                    'course:id,course_price,course_special_price,currency',
                    'customerQuote',
                    'customerQuote.installments'
                ])
                ->findOrFail($request->id);

            return view('customer-pricing.index', compact('page_title', 'ticketDetail'));
        } catch (\Exception $ex) {
            $request->session()->flash('error', __('Something went wrong, Please try again!.'));

            return redirect()->route('tickets.customer.pricing', ['id' => $request->id]);
        }
    }

    /**
     * Store the specific resource.
     *
     * @param \App\Http\Requests\CustomerQuoteRequest
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerQuoteRequest $request)
    {
        try {
            $ticket = Ticket::findOrFail($request->ticket_id);
            if (!$this->validateQuoteAmount($request)) {
                $msg = ($ticket->ticket_type_id == config('constants.TICKET_TYPE.TRAINING_KEY')) ? 'Price must be less than course special price' : 'Price must be match minimum pricing criteria';
                $request->session()->flash('error', __($msg));

                return redirect()->route('tickets.customer.pricing', ['id' => $request->id]);
            }
            $quote = $ticket->customerQuote()->updateOrCreate(['customer_id' => $request->customer_id], $request->all());
            if ($quote) {
                $quoteFlag = '';
                $quoteChanged = true;
                if (!$quote->wasRecentlyCreated && !$quote->wasChanged()) {
                    $quoteChanged = false;
                }
                if (!$quote->wasRecentlyCreated && $quote->wasChanged()) {
                    $quoteFlag = 'Edited';
                    $quote->installments()->delete();
                } else if ($quote->wasRecentlyCreated) {
                    $quoteFlag = 'added';
                }

                if ($quoteChanged) {
                    // send notification
                    $notificationDataTrainer = [
                        'sender_id' => \Auth::user()->id,
                        'receiver_id' => $quote->customer_id,
                        'sender_type' => config('constants.NOTIFICATION_TYPE.USER'),
                        'receiver_type' => config('constants.NOTIFICATION_TYPE.CUSTOMER'),
                        'title' => 'Quote added',
                        'message' => 'Quote ' . $quoteFlag . ' for the ticket ' . $ticket->ticket_id . '. check the link now',
                        'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.TICKET'),
                        'push_notification' => 1
                    ];
                    event(new SendNotification($ticket, $notificationDataTrainer));
                    // Save ticket log
                    event(new TicketLog($quote, 'Admin ' . \Auth::user()->full_name . ' has ' . $quoteFlag . ' customer quote.'));                    
                    $quote->installments()->create(['name' => 'Final Amount', 'amount' => $quote->quote, 'currency' => $quote->currency, 'due_date' => Carbon::now()->addDays(2)]);
                    $request->session()->flash('success', __("Customer pricing $quoteFlag successfully."));
                }

                return redirect()->route('tickets.customer.pricing', ['id' => $request->id]);
            }
            $request->session()->flash('error', __('Can not add customer pricing, Please try again!.'));

            return redirect()->route('tickets.customer.pricing', ['id' => $request->id]);
        } catch (\Exception $ex) {
            $request->session()->flash('error', __('Something went wrong, Please try again!.'));

            return redirect()->route('tickets.customer.pricing', ['id' => $request->id]);
        }
    }

    /**
     * Show form to store the specific resource.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function createInstallment(Request $request)
    {
        return view('customer-pricing.installment-form');
    }

    /**
     * Store the specific resource.
     *
     * @param \App\Http\Requests\InstallmentRequest
     * @return \Illuminate\Http\Response
     */
    public function storeInstallment(InstallmentRequest $request)
    {
        try {
            if (!$this->validateInstallmentAmount($request)) {
                $request->session()->flash('error', __('You have reached the maximun amount limit for installment!.'));

                return redirect()->route('tickets.customer.pricing', ['id' => $request->id]);
            }
            $customerQuote = CustomerQuote::where('ticket_id', $request->id)->findOrFail($request->customer_quote_id);

            if ($customerQuote->installments()->count() === 1) {
                $customerQuote->installments()->update(['name' => 'First Installment']);
            }
            $request->merge(['currency' => $customerQuote->currency]);
            $installment = $customerQuote->installments()->create($request->all());

            if ($installment) {
                // Save ticket log
                event(new TicketLog($customerQuote, 'Admin ' . \Auth::user()->full_name . ' has created new installment.'));

                $request->session()->flash('success', __('Installment created successfully.'));

                return redirect()->route('tickets.customer.pricing', ['id' => $request->id]);
            }
            $request->session()->flash('error', __('Can not create installment, Please try again!.'));

            return redirect()->route('tickets.customer.pricing', ['id' => $request->id]);
        } catch (\Exception $ex) {
            $request->session()->flash('error', __('Something went wrong, Please try again!.'));

            return redirect()->route('tickets.customer.pricing', ['id' => $request->id]);
        }
    }

    /**
     * Show form to edit the specific resource.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function editInstallment(Request $request)
    {
        try {
            $installment = CustomerQuoteInstallment::findOrFail($request->installment_id);

            return view('customer-pricing.installment-form', compact('installment'));
        } catch (\Exception $ex) {
            $request->session()->flash('error', __('Something went wrong, Please try again!.'));

            return redirect()->route('tickets.customer.pricing', ['id' => $request->id]);
        }
    }

    /**
     * Update the specific resource.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function updateInstallment(InstallmentRequest $request)
    {
        try {
            if (!$this->validateInstallmentAmount($request)) {
                $request->session()->flash('error', __('You have reached the maximun amount limit for installment!.'));

                return redirect()->route('tickets.customer.pricing', ['id' => $request->id]);
            }
            $installment = CustomerQuoteInstallment::findOrFail($request->installment_id);
            if ($installment->payment_status == config('constants.PAYMENT.PAID')) {
                $request->session()->flash('error', __('Can not edit this installment as it is paid by the customer!.'));

                return redirect()->route('tickets.customer.pricing', ['id' => $request->id]);
            }
            if ($installment->update($request->all())) {
                // Save ticket log
                event(new TicketLog($installment->customerQuote, 'Admin ' . \Auth::user()->full_name . ' has edited installment.'));

                $request->session()->flash('success', __('Installment edited successfully.'));

                return redirect()->route('tickets.customer.pricing', ['id' => $request->id]);
            }
            $request->session()->flash('error', __('Can not edit installment, Please try again!.'));

            return redirect()->route('tickets.customer.pricing', ['id' => $request->id]);
        } catch (\Exception $ex) {
            $request->session()->flash('error', __('Something went wrong, Please try again!.'));

            return redirect()->route('tickets.customer.pricing', ['id' => $request->id]);
        }
    }

    /**
     * View the specific resource.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function viewInstallment(Request $request)
    {
        try {
            $installment = CustomerQuoteInstallment::findOrFail($request->installment_id);

            return view('customer-pricing.view', compact('installment'));
        } catch (\Exception $ex) {
            $request->session()->flash('error', __('Something went wrong, Please try again!.'));

            return redirect()->route('tickets.customer.pricing', ['id' => $request->id]);
        }
    }

    /**
     * Delete the specific resource.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function deleteInstallment(Request $request)
    {
        try {
            $installment = CustomerQuoteInstallment::findOrFail($request->installment_id);
            if ($installment->payment_status == config('constants.PAYMENT.PAID')) {
                $request->session()->flash('error', __('Can not delete this installment as it is paid by the customer!.'));

                return redirect()->route('tickets.customer.pricing', ['id' => $request->id]);
            }
            if ($installment->delete()) {
                // Save ticket log
                event(new TicketLog($installment->customerQuote, 'Admin ' . \Auth::user()->full_name . ' has deleted installment.'));

                $request->session()->flash('success', __('Installment deleted successfully.'));

                return redirect()->route('tickets.customer.pricing', ['id' => $request->id]);
            }
            $request->session()->flash('error', __('Can not delete installment, Please try again!.'));

            return redirect()->route('tickets.customer.pricing', ['id' => $request->id]);
        } catch (\Exception $ex) {
            $request->session()->flash('error', __('Something went wrong, Please try again!.'));

            return redirect()->route('tickets.customer.pricing', ['id' => $request->id]);
        }
    }

    /**
     * Check installment amount
     */
    public function checkInstallmentAmount(Request $request)
    {
        try {
            return ($this->validateInstallmentAmount($request)) ? 'true' : 'false';
        } catch (\Exception $ex) {
            return 'false';
        }
    }

    /**
     * Check quote amount
     */
    public function checkQuoteAmount(Request $request)
    {
        try {
            return ($this->validateQuoteAmount($request)) ? 'true' : 'false';
        } catch (\Exception $ex) {
            return 'false';
        }
    }

    /**
     * Validate installment amount
     */
    public function validateInstallmentAmount(Request $request)
    {
        try {
            $search = [];
            if (!empty($request->installment_id)) {
                $search[] = ['id', '<>', $request->installment_id];
            }
            $customerQuote = CustomerQuote::withCount(['installments as total_installment_amount' => function ($query) use ($search) {
                $query->select(\DB::raw('IFNULL(SUM(amount),0)'))->where($search);
            }])->where('ticket_id', $request->id)->findOrFail($request->customer_quote_id);

            $totalAmount = ($customerQuote->total_installment_amount + $request->amount);
            if ($totalAmount > $customerQuote->quote) {
                return false;
            }

            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * Validate quote amount
     */
    public function validateQuoteAmount(Request $request)
    {
        try {
            $ticket = Ticket::with(['course:id,course_special_price,currency'])->findOrFail($request->id);
            switch($ticket->ticket_type_id) {
                case config('constants.TICKET_TYPE.TRAINING_KEY'):
                    return $this->validateTrainingType($request, $ticket);
                    break;
                case config('constants.TICKET_TYPE.JOB_SUPPORT_KEY'):
                    return $this->validateJobSupportType($request, $ticket);
                    break;
                case config('constants.TICKET_TYPE.INTERVIEW_SUPPORT_KEY'):
                    return $this->validateInterviewSupportType($request, $ticket);
                    break;
            }

            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * Validate training type quote
     */
    public function validateTrainingType(Request $request, $ticket) {
        try {
            if ($ticket->course && $request->currency == $ticket->course->currency && trim($request->quote) > $ticket->course->course_special_price) {
                return false;
            }

            if($ticket->course && $request->currency != $ticket->course->currency) {
                $quotePrice = CommonHelper::foreignExchangePrice($ticket->course->currency, $ticket->course->course_special_price);

                if ($quotePrice && trim($request->quote) > $quotePrice) {
                    return false;
                }
            }

            return true;
        } catch(\Exception $ex) {
            return false;
        }
    }

    /**
     * Validate job support type quote
     */
    public function validateJobSupportType(Request $request, $ticket) {
        try {
            $minPrice = $this->getMinPrice($ticket->ticket_type_id, $request->currency);
            if($minPrice && $request->quote < $minPrice) {
                return false;
            }

            return true;
        } catch(\Exception $ex) {
            return false;
        }
    }

    /**
     * Validate interview support type quote
     */
    public function validateInterviewSupportType(Request $request, $ticket) {
        try {
            $minPrice = $this->getMinPrice($ticket->ticket_type_id, $request->currency);
            if($minPrice && $request->quote < $minPrice) {
                return false;
            }

            return true;
        } catch(\Exception $ex) {
            return false;
        }
    }

    /**
     * Get minimun customer price based on ticket type and currency
     */
    public function getMinPrice($ticketType, $currency) {
        if($ticketType && $currency) {
            switch($ticketType) {
                case config('constants.TICKET_TYPE.JOB_SUPPORT_KEY'):
                    return ($currency === config('constants.CURRENCY.INR')) ? config('constants.TICKET_TYPE.JOB_SUPPORT_MIN_INR_PRICE') : config('constants.TICKET_TYPE.JOB_SUPPORT_MIN_USD_PRICE');
                    break;
                case config('constants.TICKET_TYPE.INTERVIEW_SUPPORT_KEY'):
                    return ($currency === config('constants.CURRENCY.INR')) ? config('constants.TICKET_TYPE.INTERVIEW_SUPPORT_MIN_INR_PRICE') : config('constants.TICKET_TYPE.INTERVIEW_SUPPORT_MIN_USD_PRICE');
                    break;
            }

            return '';
        }

        return '';
    }

    /**
     * Get price rate baed on the currency
     */
    public function checkPriceRate(Request $request) {
        try {
            if ($request->ajax() && !empty($request->ticket_id) && !empty($request->currency)) {
                $ticket = Ticket::with(['course:id,course_special_price,currency'])->findOrFail($request->ticket_id);
                $price = $courseSpeciaPrice = ($ticket->course) ? $ticket->course->course_special_price : 0;
                if($request->currency != $ticket->course->currency) {
                    $price = CommonHelper::foreignExchangePrice($ticket->course->currency, $courseSpeciaPrice);
                }

                return response()->json(['status' => 1, 'price' => $price], 200);
            }

            return response()->json(['status' => 0], 500);
        } catch(\Exception $ex) {
            return response()->json(['status' => 0], 500);
        }
    }
}
