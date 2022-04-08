<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\InvoiceRequest;
use App\Http\Requests\PaymentCheckRequest;
use App\Http\Requests\PaymentReadRequest;
use App\Http\Response\ApiResponse;
use App\Models\CustomerQuoteInstallment;
use App\Models\PaymentLog;
use App\Models\Ticket;
use App\Models\TrainerQuote;
use App\Events\TicketLog;
use App\Events\SendNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use CommonHelper;
use Carbon\Carbon;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use Str;

class PaymentController extends ApiController
{
    /**
     * Current active menu of sidebar
     * 
     * @var string
     */
    public $api;

    /**
     * Instantiate a new controller instance
     * 
     * @return void
     */
    public function __construct(ApiResponse $apiResponse)
    {
        $this->api = new Api(config('constants.RAZORPAY_KEY_ID'), config('constants.RAZORPAY_KEY_SECRET'));
        parent::__construct($apiResponse);
    }

    /**
     * URL - {{local}}/v1/ticket/{id}/payments
     * METHOD - GET
     * Get the payment details
     * 
     * @param \Illuminate\Http\Request $request
     * @return object|JsonResponse
     */
    public function payments(Request $request)
    {
        try {
            if (\Auth::guard('trainer')->check()) {
                $loggedinId = \Auth::guard('trainer')->user()->id;
            } else {
                $loggedinId = \Auth::guard('customer')->user()->id;
            }
            $paymentDetails = Ticket::select('id', 'customer_id', 'trainer_id')
                ->withCount(['trainerInvoices as total_invoice_amount' => function ($query) use ($loggedinId) {
                    $query->where('trainer_id', $loggedinId)->select(\DB::raw('IFNULL(SUM(amount),0)'));
                }])
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

            $response = [];
            if (empty($paymentDetails->customerQuote)) {
                $response['customer_quote'] =  null;
            } else {
                $response['customer_quote'] = $paymentDetails->customerQuote ?? null;
                $response['customer_quote']['next_due_payment'] = ($paymentDetails->customerQuote && $paymentDetails->customerQuote->installments->isNotEmpty()) ? $paymentDetails->customerQuote->installments->where('payment_status', config('constants.PAYMENT.DUE'))->first() : null;
            }

            $response['trainer_quote'] = ($paymentDetails->trainer && $paymentDetails->trainer->quotes->isNotEmpty()) ? $paymentDetails->trainer->quotes : null;
            if ($response['trainer_quote'] && $response['trainer_quote']->count() > 0) {
                $trainerQuote = $response['trainer_quote']->first();
                $trainerQuote->total_invoice_amount = $paymentDetails->total_invoice_amount ?? 0;
            }
            return $this->apiResponse->respondWithMessageAndPayload($response);
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * URL - {{local}}/v1/ticket/quote/{quote_id}/create-invoice
     * METHOD - GET
     * Create invoice
     * 
     * @param \App\Http\Requests\InvoiceRequest $request
     * @return object|JsonResponse
     */
    public function createInvoice(InvoiceRequest $request)
    {
        try {
            $quote = TrainerQuote::withCount(['invoices as total_invoice_amount' => function ($query) {
                $query->select(\DB::raw('IFNULL(SUM(amount),0)'));
            }])->findOrFail($request->quote_id);
            $ticket = Ticket::select(['id', 'status'])->findOrFail($quote->ticket_id);
            if (in_array($ticket->status, [config('constants.TICKET.INACTIVE')])) {
                $message = in_array($ticket->status, [config('constants.TICKET.INACTIVE')]) ? strtolower($ticket->status_label) : '';

                return $this->apiResponse->respondWithError(__("Can not create invoice as this ticket is " . $message));
            }
            $totalAmount = ($request->amount + $quote->total_invoice_amount);
            if ($totalAmount > $quote->quote) {
                return $this->apiResponse->respondWithError(__("You have reached the maximun amount limit for invoice"));
            }
            $request->merge([
                'currency' => $quote->currency,
                'invoice_number' => CommonHelper::generateInvoiceNumber(),
                'invoice_date' => Carbon::now()->toDateString()
            ]);
            if ($quote->invoices()->create($request->only('amount', 'currency', 'invoice_number', 'invoice_date'))) {
                // send notification to sub admin
                if (config('constants.ADMIN_ROLE.SUPER_ADMIN') != $quote->ticket->user_id) {
                    $notificationSubadmin = [
                        'sender_id' => \Auth::guard('trainer')->user()->id,
                        'receiver_id' => $quote->ticket->user_id,
                        'sender_type' => config('constants.NOTIFICATION_TYPE.TRAINER'),
                        'receiver_type' => config('constants.NOTIFICATION_TYPE.USER'),
                        'title' => 'Invoice created',
                        'message' => $quote->trainer->full_name . ' has created an invoice for the ticket ' . $quote->ticket->ticket_id . '.',
                        'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.TRAINER_QUOTE'),
                        'push_notification' => 0
                    ];
                    event(new SendNotification($quote, $notificationSubadmin));
                }
                // send notification to super admin
                $notificationSuperAdmin = [
                    'sender_id' => \Auth::guard('trainer')->user()->id,
                    'receiver_id' => config('constants.ADMIN_ROLE.SUPER_ADMIN'),
                    'sender_type' => config('constants.NOTIFICATION_TYPE.TRAINER'),
                    'receiver_type' => config('constants.NOTIFICATION_TYPE.USER'),
                    'title' => 'Invoice created',
                    'message' => $quote->trainer->full_name . ' has created an invoice for the ticket ' . $quote->ticket->ticket_id . '.',
                    'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.TRAINER_QUOTE'),
                    'push_notification' => 0
                ];
                event(new SendNotification($quote, $notificationSuperAdmin));
                // Save ticket log
                event(new TicketLog($quote, 'Trainer ' . \Auth::guard('trainer')->user()->full_name . ' has created an invoice.'));

                return $this->apiResponse->respondWithMessage(__('Invoice created successfully'));
            }

            return $this->apiResponse->respondWithError(__("Can not create invoice, Please try again!"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * URL - {{local}}/v1/customer/ticket/create-order/{installment_id}
     * METHOD - GET
     * Create order
     * 
     * @param \Illuminate\Http\Request $request
     * @return object|JsonResponse
     */
    public function createOrder(Request $request)
    {
        try {
            $installment = CustomerQuoteInstallment::findOrFail($request->installment_id);
            $ticket = Ticket::findOrFail($installment->customerQuote->ticket_id);
            if (in_array($ticket->status, [config('constants.TICKET.COMPLETE'), config('constants.TICKET.INACTIVE'), config('constants.TICKET.CANCEL')])) {
                $message = in_array($ticket->status, [config('constants.TICKET.COMPLETE'), config('constants.TICKET.INACTIVE'), config('constants.TICKET.CANCEL')]) ? strtolower($ticket->status_label) : '';

                return $this->apiResponse->respondWithError(__("Can not make payment as this ticket is " . $message));
            }
            $order = $this->api->order->create([
                'receipt' => Str::random(8),
                'amount' => ($installment->amount * 100),
                'currency' => $installment->currency,
                'payment_capture' => 1 // auto capture
            ]);
            $installment->order_id = $order->id;

            if ($installment->save()) {
                return $this->apiResponse->respondWithMessageAndPayload(['order_id' => $installment->order_id], __("Order created successfully"));
            }

            return $this->apiResponse->respondWithError(__("Can not create order, Please try again!"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * URL - {{local}}/v1/customer/ticket/{ticket_id}/payment/{installment_id}
     * METHOD - GET
     * Check payment status
     * 
     * @param \App\Http\Requests\PaymentCheckRequest $request
     * @return object|JsonResponse
     */
    public function checkPaymentStatus(PaymentCheckRequest $request)
    {
        try {
            $attributes = json_decode($request->api_response, true);
            if ($request->status == 1) {
                if ($this->verifySignature($attributes)) {
                    $installment = CustomerQuoteInstallment::findOrFail($request->installment_id);
                    $installment->payment_status = config('constants.PAYMENT.PAID');
                    $installment->invoice_number = CommonHelper::generateInvoiceNumber();
                    $installment->invoice_date = Carbon::now()->toDateString();
                    if ($installment->save()) {
                        // Save ticket log
                        $message = 'Customer ' . \Auth::guard('customer')->user()->full_name . ' has made a payment.';
                        event(new TicketLog($installment->customerQuote, $message));

                        $attributes['ticket_id'] = $request->ticket_id;
                        $attributes['customer_quote_installment_id'] = $request->installment_id;
                        $request->merge($attributes);
                        PaymentLog::create($request->all());
                        // send notification to sub admin
                        if (config('constants.ADMIN_ROLE.SUPER_ADMIN') != $installment->customerQuote->ticket->user_id) {
                            $notificationSubadmin = [
                                'sender_id' => \Auth::guard('customer')->user()->id,
                                'receiver_id' => $installment->customerQuote->ticket->user_id,
                                'sender_type' => config('constants.NOTIFICATION_TYPE.CUSTOMER'),
                                'receiver_type' => config('constants.NOTIFICATION_TYPE.USER'),
                                'title' => 'Customer made a payment',
                                'message' => $message,
                                'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.CUSTOMER_PAYMENT'),
                                'push_notification' => 0
                            ];
                            event(new SendNotification($installment, $notificationSubadmin));
                        }
                        // send notification to super admin
                        $notificationSuperAdmin = [
                            'sender_id' => \Auth::guard('customer')->user()->id,
                            'receiver_id' => config('constants.ADMIN_ROLE.SUPER_ADMIN'),
                            'sender_type' => config('constants.NOTIFICATION_TYPE.CUSTOMER'),
                            'receiver_type' => config('constants.NOTIFICATION_TYPE.USER'),
                            'title' => 'Customer made a payment',
                            'message' => $message,
                            'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.CUSTOMER_PAYMENT'),
                            'push_notification' => 0
                        ];
                        event(new SendNotification($installment, $notificationSuperAdmin));

                        return $this->apiResponse->respondWithMessage(__("Payment successful"));
                    }
                }
                return $this->apiResponse->respondWithError(__("Payment failed, Please try again!"));
            } else {
                $error = json_decode($attributes['description'], true);
                $paymentLog = isset($error['error']) ? $error['error'] : $error;
                $errorLog = [
                    'ticket_id' => $request->ticket_id,
                    'customer_quote_installment_id' => $request->installment_id,
                    'error_code' => $paymentLog['code'] ?? '',
                    'error_description' => $paymentLog['description'] ?? '',
                    'error_reason' => $paymentLog['reason'] ?? '',
                ];
                $request->merge($errorLog);
                PaymentLog::create($request->all());

                return $this->apiResponse->respondWithError(__("Payment failed, Please try again!"));
            }
            return $this->apiResponse->respondWithError(__("Something went wrong, Please try again!"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Verify payment signature
     * 
     * @param \Illuminate\Http\Request $request
     * @return object|JsonResponse
     */
    public function verifySignature($data)
    {
        try {
            $attributes = [
                'razorpay_order_id' => $data['razorpay_order_id'],
                'razorpay_payment_id' => $data['razorpay_payment_id'],
                'razorpay_signature' => $data['razorpay_signature']
            ];
            $this->api->utility->verifyPaymentSignature($attributes);

            return true;
        } catch (SignatureVerificationError $ex) {
            $error = 'Razorpay Error : ' . $ex->getMessage();
            return false;
        }
    }

    /**
     * Mark payment update as read
     * 
     * @param \App\Http\Requests\PaymentReadRequest $request
     * @return object|JsonResponse
     */
    public function markAsRead(PaymentReadRequest $request)
    {
        try {
            $ticket = Ticket::findOrFail($request->ticket_id);
            if (\Auth::guard('trainer')->check()) {
                $loggedinId = \Auth::guard('trainer')->user()->id;
                $record = $ticket->trainerInvoices()->where('trainer_id', '=', $loggedinId);
            } else {
                $loggedinId = \Auth::guard('customer')->user()->id;
                $record = $ticket->customerInstallments()->where('customer_id', '=', $loggedinId);
            }

            if ($record->count() > 0 && $record->update(['is_read' => config('constants.PAYMENT.READ_NOTIFICATION')])) {
                return $this->apiResponse->respondWithMessage();
            }

            return $this->apiResponse->respondWithMessage(__("No any payment details found!"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }
}
