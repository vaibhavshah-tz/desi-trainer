<?php

namespace App\Models;

use Carbon\Carbon;
use CommonHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainerQuoteInvoice extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trainer_quote_id', 'name', 'amount', 'payment_status', 'currency', 'invoice_number', 'invoice_date', 'file', 'is_read'
    ];

    /**
     * The attributes that are appends to result set.
     *
     * @var array
     */
    protected $appends = ['amount_label', 'amount_without_label', 'payment_status_label', 'formated_invoice_date'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saving(function ($data) {
            if ($data->isDirty('payment_status')) {
                $data->is_read = config('constants.PAYMENT.UNREAD_NOTIFICATION');
            }
        });
        static::saved(function ($data) {
            $quote = $data->trainerQuote;
            $paidAmount = $data->getTotalPaidAmount();
            if ($paidAmount == $quote->quote) {
                $quote->payment_status = config('constants.PAYMENT.PAID');
            } else {
                $quote->payment_status = config('constants.PAYMENT.DUE');
            }
            $quote->save();
        });
        static::deleting(function ($data) {
            $data->notifications()->delete();
        });
    }

    /**
     * Get the quote that owns the installment.
     */
    public function trainerQuote()
    {
        return $this->belongsTo(TrainerQuote::class);
    }

    /**
     * Set the quote value with currency
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
     * get the payment status label
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
        $amount = self::select('amount')->where([['trainer_quote_id', $this->trainerQuote->id], ['payment_status', config('constants.PAYMENT.PAID')]])->sum('amount');

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
            $amount = ($this->trainerQuote->quote - $this->getTotalPaidAmount());

            return $this->attributes['currency'] . ' ' . CommonHelper::formatNumber($amount);
        }

        return null;
    }

    /**
     * Get the file URL
     *
     * @return string file path
     */
    public function getFileAttribute()
    {
        return !empty($this->attributes['file']) ? config('constants.IMAGE_PATH.INVOICE') . '/' . $this->attributes['file'] : '';
    }

    /**
     * Get the full URL of file
     *
     * @return string file path
     */
    public function getFileUrlAttribute()
    {
        $imageUrl = CommonHelper::getImage($this->getFileAttribute());
        $image = !empty($imageUrl) ? $imageUrl : '';

        return ($image) ? url($image) : '';
    }

    /**
     * Upload the file
     *
     * @param  string  $value file instase
     * @return string hase name
     */
    public function setFileAttribute($value)
    {
        $this->attributes['file'] = !empty($value) ? CommonHelper::uploadImage(config('constants.IMAGE_PATH.INVOICE'), $value) : null;
    }

    /**
     * Get all ticket activity logs
     */
    public function ticketLogs()
    {
        return $this->morphMany(TicketLog::class, 'ticket_log');
    }

    /**
     * Get all of the invoice notifications.
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notificationtable');
    }
}
