<?php

namespace App\Models;

use CommonHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerQuote extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ticket_id', 'customer_id', 'quote', 'payment_status', 'currency'
    ];

    /**
     * The attributes that are appends to result set.
     *
     * @var array
     */
    protected $appends = ['quote_label', 'discount', 'quote_without_label'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleting(function ($quote) {
            $quote->installments()->each(function ($installment) {
                $installment->delete();
            });
            $quote->notifications()->delete();
        });
    }

    /**
     * Get the trainer that owns the quote.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the trainer that owns the quote.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the installments for the quote.
     */
    public function installments()
    {
        return $this->hasMany(CustomerQuoteInstallment::class);
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

    /**
     * Get the discount
     *
     * @return string discount
     */
    public function getDiscountAttribute()
    {
        $ticket = $this->ticket;
        if(!empty($ticket->ticket_type_id) && $ticket->ticket_type_id == config('constants.TICKET_TYPE.TRAINING_KEY')) {
            $course = $ticket->course;
            $price = $courseSpeciaPrice = ($course) ? $course->course_special_price : 0;
            if($this->attributes['currency'] != $course->currency) {
                $price = CommonHelper::foreignExchangePrice($course->currency, $courseSpeciaPrice);
            }

            if (!empty($this->attributes['quote']) && !empty($price) && $price > $this->attributes['quote']) {
                $percent = round((($price - $this->attributes['quote']) * 100) / $price, 2) . '%';
    
                return $percent;
            }
        }

        return 0;
    }
}
