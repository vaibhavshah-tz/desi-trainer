<?php

namespace App\Models;

use CommonHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainerQuote extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ticket_id', 'trainer_id', 'quote', 'payment_status', 'currency'
    ];

    /**
     * The attributes that are appends to result set.
     *
     * @var array
     */
    protected $appends = ['quote_label', 'payment_status_label', 'quote_without_label'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleting(function ($quote) {
            $quote->invoices()->each(function ($invoice) {
                $invoice->delete();
            });
            $quote->notifications()->delete();
        });
    }

    /**
     * Get the trainer that owns the quote.
     */
    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    /**
     * Get the trainer that owns the quote.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the invoices for the quote.
     */
    public function invoices()
    {
        return $this->hasMany(TrainerQuoteInvoice::class);
    }

    /**
     * Set the quote value with currency
     * 
     * @return string
     */
    public function getQuoteLabelAttribute()
    {
        if (!empty($this->attributes['quote']) && !empty($this->attributes['currency'])) {
            return CommonHelper::formatNumber($this->attributes['currency'], $this->attributes['quote']);
        }

        return '';
    }

    /**
     * Set the quote value with currency with html label
     * 
     * @return string
     */
    public function getQuoteWithoutLabelAttribute()
    {
        if (!empty($this->attributes['quote']) && !empty($this->attributes['currency'])) {
            $price = CommonHelper::formatPrice($this->attributes['quote']);

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
     * Get all ticket activity logs
     */
    public function ticketLogs()
    {
        return $this->morphMany(TicketLog::class, 'ticket_log');
    }

    /**
     * Get all of the quote notifications.
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notificationtable');
    }
}
