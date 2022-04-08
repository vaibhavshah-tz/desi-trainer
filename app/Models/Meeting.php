<?php

namespace App\Models;

use App\Helpers\CommonHelper;
use app\Models\User;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meeting extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'ticket_id', 'customer_id', 'trainer_id', 'timezone_id',
        'meeting_title', 'date', 'time', 'meeting_url', 'meeting_timestamp', 'status', 'create_meeting_with'
    ];

    /**
     * The attributes that are appends to result set.
     *
     * @var array
     */
    protected $appends = ['date_formated', 'time_formated'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleting(function ($meeting) {
            $meeting->notifications()->delete();
        });
    }

    /**
     * Scope a query to only get active customer.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('meetings.status', config('constants.ACTIVE'));
    }

    /**
     * Get the user that owns the meeting.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Get the ticket that owns the meeting.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the customer that owns the meeting.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the trainer that owns the meeting.
     */
    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    /**
     * Get the timezone that owns the meeting.
     */
    public function timezone()
    {
        return $this->belongsTo(Timezone::class);
    }

    public function getDateFormatedAttribute()
    {
        if (!empty($this->attributes['date'])) {
            $date = Carbon::parse($this->attributes['date']);

            return $date->format('jS F Y');
        }

        return '';
    }

    /**
     * Set the timez value with timezone
     * 
     * @return string
     */
    public function getTimeFormatedAttribute()
    {
        $timeWithAbbr = '';
        if (!empty($this->attributes['time'])) {
            $timeWithAbbr = CommonHelper::timeWithAbbreviation($this->attributes['time'], $this->timezone);
        }

        return trim($timeWithAbbr);
    }

    /**
     * Get the meeting date time
     *
     * @return string 
     */
    public function getMeetingDateTimeAttribute()
    {
        if (!empty($this->attributes['date']) && !empty($this->attributes['time'])) {
            $date = Carbon::parse($this->attributes['date'])->toDateString();
            $datetime = Carbon::parse($date . ' ' . $this->attributes['time']);

            return $datetime->format('Y-m-d h:i A');
        }

        return '';
    }

    /**
     * Get all of the meeting notifications.
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notificationtable');
    }

    /**
     * Get all ticket activity logs
     */
    public function ticketLogs()
    {
        return $this->morphMany(TicketLog::class, 'ticket_log');
    }
}
