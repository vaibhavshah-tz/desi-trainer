<?php

namespace App\Models;

use CommonHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerQuoteInstallment extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_quote_id', 'name', 'amount', 'payment_status', 'currency', 'due_date', 'invoice_number', 'invoice_date', 'order_id', 'is_read'
    ];

    /**
     * The attributes that are appends to result set.
     *
     * @var array
     */
    protected $appends = ['amount_label', 'amount_without_label', 'payment_status_label', 'formated_due_date', 'formated_invoice_date'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saving(function ($data) {
            if ($data->isDirty() && !$data->isDirty('payment_status')) {
                $data->is_read = config('constants.PAYMENT.UNREAD_NOTIFICATION');
            }
        });
        static::deleting(function ($data) {
            $data->notifications()->delete();
        });
    }

    /**
     * Get the quote that owns the installment.
     */
    public function customerQuote()
    {
        return $this->belongsTo(CustomerQuote::class);
    }

    /**
     * payment log is associated with ticket installment
     */
    public function paymentLogs()
    {
        return $this->hasMany(PaymentLog::class);
    }

    /**
     * Set the amount value with currency
     * 
     * @return string
     */
    public function getAmountLabelAttribute()
    {
        if (!empty($this->attributes['amount']) && !empty($this->attributes['currency'])) {
            return CommonHelper::formatNumber($this->attributes['currency'], $this->attributes['amount']);
        }

        return '';
    }

    /**
     * Set the amount value with currency with html label
     * 
     * @return string
     */
    public function getAmountWithoutLabelAttribute()
    {
        if (!empty($this->attributes['amount']) && !empty($this->attributes['currency'])) {
            $price = CommonHelper::formatPrice($this->attributes['amount']);

            return $this->attributes['currency'] . ' ' . $price;
        }

        return '';
    }

    /**
     * Set the timez value with timezone
     * 
     * @return string
     */
    public function getFormatedDueDateAttribute()
    {
        if (!empty($this->attributes['due_date'])) {
            $date = Carbon::parse($this->attributes['due_date']);

            return $date->format('jS F Y');
        }

        return '';
    }

    /**
     * Set the quote value with currency
     * 
     * @return string
     */
    public function getPaymentStatusLabelAttribute()
    {
        if (!empty($this->attributes['payment_status'])) {
            return CommonHelper::quoteStatusLabel($this->attributes['payment_status'])['title'] ?? '';
        }

        return '';
    }

    /**
     * Set the timez value with timezone
     * 
     * @return string
     */
    public function getFormatedInvoiceDateAttribute()
    {
        if (!empty($this->attributes['invoice_date'])) {
            $date = Carbon::parse($this->attributes['invoice_date']);

            return $date->format('jS F Y');
        }

        return '';
    }

    /**
     * Get all ticket activity logs
     */
    public function ticketLogs()
    {
        return $this->morphMany(TicketLog::class, 'ticket_log');
    }

    /**
     * Get all of the installment notifications.
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notificationtable');
    }

    /**
     * Get paid amount
     * 
     * @return string
     */
    public function getAmountPaidAttribute()
    {
        if (!empty($this->attributes['payment_status']) && $this->attributes['payment_status'] == config('constants.PAYMENT.PAID')) {

            return $this->getAmountLabelAttribute();
        }

        return null;
    }

    /**
     * Get total paid amount
     * 
     * @return string
     */
    public function getTotalPaidAmount()
    {
        $amount = self::select('amount')->where([['customer_quote_id', $this->customerQuote->id], ['payment_status', config('constants.PAYMENT.PAID')]])->sum('amount');

        return $amount;
    }

    /**
     * Get due amount
     * 
     * @return string
     */
    public function getAmountDueAttribute()
    {
        if (!empty($this->attributes['payment_status']) && $this->attributes['payment_status'] != config('constants.PAYMENT.PAID')) {
            $amount = ($this->customerQuote->quote - $this->getTotalPaidAmount());

            return CommonHelper::formatNumber($this->attributes['currency'], $amount);
        }

        return null;
    }
}
