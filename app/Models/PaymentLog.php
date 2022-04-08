<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ticket_id', 'razorpay_order_id', 'razorpay_payment_id', 'razorpay_signature', 'error_code',
        'error_description', 'error_reason', 'api_response', 'status', 'customer_quote_installment_id'
    ];

    /**
     * Get the ticket that owns the log.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the ticket that owns the log.
     */
    public function ticketInstallment()
    {
        return $this->belongsTo(CustomerQuoteInstallment::class);
    }
}
