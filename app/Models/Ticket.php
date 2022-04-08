<?php

namespace App\Models;

use Carbon\Carbon;
use CommonHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;

class Ticket extends Model
{
    use SoftDeletes;

    private $redirectionType = 0;

    private $colorFlag;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'customer_id', 'trainer_id', 'course_category_id', 'course_id', 'timezone_id',
        'ticket_type_id', 'is_for_employee', 'other_course_category', 'other_course',
        'other_primary_skill', 'message', 'ticket_id', 'date', 'time', 'status',
        'ticket_timestamp', 'customer_budget', 'request_ticket_close', 'assigned_trainer_date', 'is_request_demo',
        'is_global'
    ];

    /**
     * The attributes that are appends to result set.
     *
     * @var array
     */
    protected $appends = ['status_label', 'default_cover_url', 'formated_created_at', 'ticket_date_time', 'date_formated', 'time_formated', 'global_request_status', 'interested_ticket_status', 'redirection_type', 'color_flag', 'ticket_status_label', 'assigned_ticket_color_flag', 'posted_date'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleted(function ($ticket) {
            $ticket->meetings()->each(function ($meeting) {
                $meeting->delete();
            });
            $ticket->proposals()->each(function ($proposal) {
                $proposal->delete();
            });
            $ticket->notifications()->delete();
            $ticket->interestedTrainers()->detach();
            $ticket->trainerQuote()->each(function ($quote) {
                $quote->delete();
            });
            $ticket->customerQuote()->each(function ($quote) {
                $quote->delete();
            });
        });
    }

    /**
     * Get the employees for the ticket.
     */
    public function ticketEmployees()
    {
        return $this->hasMany(TicketEmployee::class);
    }

    /**
     * The primary skills that belong to the ticket.
     */
    public function primarySkills()
    {
        return $this->belongsToMany(PrimarySkill::class)->withTimestamps();
    }

    /**
     * The trainers that belong to the ticket.
     */
    public function interestedTrainers()
    {
        return $this->belongsToMany(Trainer::class, 'interested_ticket_trainer')
            ->withPivot(['is_read'])
            ->withTimestamps();
    }

    /**
     * Save primary skills data
     * 
     * @param object $ticket
     * @param array $primarySkill
     */
    public function savePrimarySkill($ticket, $primarySkill = [])
    {
        // if (!empty($primarySkill)) {
        $ticket->primarySkills()->sync($primarySkill);
        // }
        return true;
    }

    /**
     * Save employees data
     * 
     * @param object $ticket
     * @param array $ticketEmployees
     */
    public function saveTicketEmployees($ticket, $ticketEmployees = [])
    {
        if (!empty($ticketEmployees)) {
            $ticket->ticketEmployees()->delete();
            $ticket->ticketEmployees()->createMany($ticketEmployees);
        }
        return true;
    }

    /** 
     * The trainers that belong to the ticket.
     */
    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    /**
     * The customers that belong to the ticket.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * The course category that belong to the ticket.
     */
    public function courseCategory()
    {
        return $this->belongsTo(CourseCategory::class);
    }

    /**
     * The course that belong to the ticket.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * The ticket type that belong to the ticket.
     */
    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    /**
     * Get the meetings that owns the tickets.
     */
    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    /**
     * Get the proposal that owns the tickets.
     */
    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    /**
     * The timezone that belong to the ticket.
     */
    public function timezone()
    {
        return $this->belongsTo(Timezone::class);
    }

    /**
     * The users that belong to the ticket.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the status label
     *
     * @return string image path
     */
    public function getStatusLabelAttribute()
    {
        if (!empty($this->attributes['status'])) {
            return CommonHelper::getTicketStatus()[$this->attributes['status']] ?? '';
        }

        return '';
    }

    /**
     * Get the default cover image URL
     *
     * @return string image path
     */
    public function getDefaultCoverUrlAttribute()
    {
        return url(config('constants.IMAGE_PATH.DEFAULT_COVER_IMAGE'));
    }

    /**
     * Get the formated created at attribute
     *
     * @return string 
     */
    public function getFormatedCreatedAtAttribute()
    {
        if (!empty($this->attributes['created_at'])) {
            return Carbon::parse($this->attributes['created_at'])->format('d M Y h:i A');
        }

        return '';
    }

    /**
     * Get the formated created at attribute
     * use the key posted_date
     * dispay on global request
     *
     * @return string 
     */
    public function getPostedDateAttribute()
    {
        if (!empty($this->attributes['created_at'])) {
            return Carbon::parse($this->attributes['created_at'])->format('d M Y');
        }

        return '';
    }

    /**
     * Get the ticket date time
     *
     * @return string 
     */
    public function getTicketDateTimeAttribute()
    {
        if (!empty($this->attributes['ticket_timestamp'])) {
            $timezone = $this->timezone;

            return CommonHelper::convertDateToSpecificTimezone(date('Y-m-d H:i:s', $this->attributes['ticket_timestamp']), 'Asia/Kolkata')->format('d M Y h:i A') . ' IST';
        }

        return '';
    }

    /**
     * Set the ticket unique id on henader
     * Set and get ticket ID the value in session
     * 
     * @param $id ticket PK int
     * @return ticket unique ID string
     */
    public static function getTicketId($id)
    {
        if (!Session::has('TICKET_ID') || Session::get('TICKET_PK') != $id || Session::has('TICKET_DETAIL')) {
            $ticketDetails = self::select('id', 'ticket_id', 'status', 'customer_id', 'trainer_id', 'user_id')->findorfail($id);
            Session::put('TICKET_ID', $ticketDetails->ticket_id);
            Session::put('TICKET_PK', $ticketDetails->id);
            Session::put('TICKET_DETAIL', $ticketDetails);

            self::setTicketMessage($ticketDetails);
        }

        return Session::get('TICKET_ID') ?? config('constants.DEFAULT_MSG');
    }

    /**
     * Get formated date 
     */
    public function getDateFormatedAttribute()
    {
        if (!empty($this->attributes['date'])) {
            $date = Carbon::parse($this->attributes['date']);

            return $date->format('jS F Y');
        }

        return '';
    }

    /**
     * Get formated time 
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
     * Get all meeting notifications.
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notificationtable');
    }

    /**
     * trainer quote is associated with ticket
     */
    public function trainerQuote()
    {
        return $this->hasMany(TrainerQuote::class);
    }

    /**
     * trainer invoice is associated with ticket
     */
    public function trainerInvoices()
    {
        return $this->hasManyThrough(TrainerQuoteInvoice::class, TrainerQuote::class);
    }

    /**
     * trainer invoice is associated with ticket
     */
    public function customerInstallments()
    {
        return $this->hasManyThrough(CustomerQuoteInstallment::class, CustomerQuote::class);
    }

    /**
     * Get all ticket activity logs
     */
    public function ticketLogs()
    {
        return $this->morphMany(TicketLog::class, 'ticket_log');
    }

    /**
     * Get all user ticket
     * 
     * @param $userId
     * @return object
     */
    public static  function getUserTicket($userCondition)
    {
        return self::where([
            ['status', '<>', config('constants.TICKET.COMPLETE')],
            ['status', '<>', config('constants.TICKET.CANCEL')],
            $userCondition
        ])->get();
    }

    /**
     * Customer quote is associated with ticket
     */
    public function customerQuote()
    {
        return $this->hasOne(CustomerQuote::class);
    }

    /**
     * payment log is associated with ticket
     */
    public function paymentLogs()
    {
        return $this->hasMany(PaymentLog::class);
    }

    /**
     * Set other course attribute
     */
    public function setOtherCourseAttribute($value)
    {
        $this->attributes['other_course'] = (isset($this->attributes['course_id']) && $this->attributes['course_id'] != 0) ? null : $value;
    }

    /**
     * Set other course attribute
     */
    public function setOtherCourseCategoryAttribute($value)
    {
        $this->attributes['other_course_category'] = (isset($this->attributes['course_category_id']) && $this->attributes['course_category_id'] != 0) ? null : $value;
    }

    /**
     * Set the ticket close or inactive message
     * 
     * @param $status ticket status
     */
    public static function setTicketMessage($ticketDetails)
    {
        if (in_array($ticketDetails->status, [config('constants.TICKET.COMPLETE'), config('constants.TICKET.INACTIVE'), config('constants.TICKET.CANCEL')])) {
            Session::put('TICKET_STATUS_MESSAGE', "You cannot perform any action as ticket is " . strtolower($ticketDetails->status_label));
        } else {
            Session::forget('TICKET_STATUS_MESSAGE');
        }

        return;
    }

    /**
     * User is associalted with chat room
     */
    public function chatRooms()
    {
        return $this->hasMany(ChatRoom::class);
    }

    /**
     * Set the static default status for global request
     * 
     * @return string
     */
    public function getGlobalRequestStatusAttribute()
    {
        return 'New';
    }

    /**
     * Interested ticket status label
     */
    public function getInterestedTicketStatusAttribute()
    {
        if (\Auth::guard('trainer')->check() && isset($this->attributes['status']) && $this->attributes['status'] != '' && array_key_exists('trainer_id', $this->attributes)) {
            $statusLabel = '';
            $proposalSentCount = $this->proposals()->whereHas('trainers', function ($q) {
                $q->where('trainer_id', \Auth::guard('trainer')->user()->id);
            })->get()->count();

            if (in_array($this->attributes['status'], [config('constants.TICKET.INACTIVE'), config('constants.TICKET.CANCEL')])) {
                $statusLabel = 'This ticket is no longer active';
                $this->colorFlag = config('constants.MOBILE_COLOR_FLAG.GREY');
            } else if (!empty($this->attributes['trainer_id'])) {
                if ($this->attributes['trainer_id'] == \Auth::guard('trainer')->user()->id) {
                    $statusLabel = 'You have been assigned to this ticket';
                    $this->redirectionType = config('constants.NOTIFICATION_REDIRECTION_TYPE.ASSIGNED_TICKET');
                    $this->colorFlag = config('constants.MOBILE_COLOR_FLAG.SKY_BLUE');
                } else {
                    $statusLabel = 'This ticket is no longer active';
                    $this->colorFlag = config('constants.MOBILE_COLOR_FLAG.GREY');
                }
            } else if ($proposalSentCount > 0) {
                $statusLabel = 'Proposal sent to this ticket';
                $this->redirectionType = config('constants.NOTIFICATION_REDIRECTION_TYPE.PROPOSAL');
                $this->colorFlag = config('constants.MOBILE_COLOR_FLAG.PURPLE');
            } else if (is_null($this->attributes['trainer_id'])) {
                $statusLabel = 'Request sent(Admin review in progress)';
                $this->colorFlag = config('constants.MOBILE_COLOR_FLAG.RED');
            }

            return $statusLabel;
        }

        return '';
    }

    /**
     * get redirection type
     * 
     * @return string
     */
    public function getRedirectionTypeAttribute()
    {
        return $this->redirectionType;
    }

    /**
     * get status color flag
     * 
     * @return string
     */
    public function getColorFlagAttribute()
    {
        return $this->colorFlag ?? 0;
    }

    /**
     * get assigned ticket status label
     * 
     * @return string
     */
    public function getTicketStatusLabelAttribute()
    {
        if (\Auth::guard('trainer')->check() && isset($this->attributes['status']) && $this->attributes['status'] != '' && isset($this->attributes['request_ticket_close'])) {
            $ticketStatus = 'Work in progress';
            $this->colorFlag = config('constants.MOBILE_COLOR_FLAG.PURPLE');
            switch ($this->attributes['status']) {
                case config('constants.TICKET.COMPLETE'):
                    $ticketStatus = 'Completed-Approved & Verified from Admin';
                    $this->colorFlag = config('constants.MOBILE_COLOR_FLAG.GREEN');
                    break;
                case config('constants.TICKET.INACTIVE'):
                    $ticketStatus = 'This ticket is no longer active';
                    $this->colorFlag = config('constants.MOBILE_COLOR_FLAG.GREY');
                    break;
                case config('constants.TICKET.CANCEL'):
                    $ticketStatus = 'Cancelled';
                    $this->colorFlag = config('constants.MOBILE_COLOR_FLAG.DARK_BLUE');
                    break;
            }

            if (!in_array($this->attributes['status'], [config('constants.TICKET.INACTIVE'), config('constants.TICKET.CANCEL'), config('constants.TICKET.COMPLETE')]) && $this->attributes['request_ticket_close'] == 1) {
                $ticketStatus = 'Completed (Review pending from admin)';
                $this->colorFlag = config('constants.MOBILE_COLOR_FLAG.SKY_BLUE');
            }

            return $ticketStatus;
        }

        return '';
    }

    /**
     * get assigned ticket status color flag
     * 
     * @return string
     */
    public function getAssignedTicketColorFlagAttribute()
    {
        return $this->colorFlag ?? 0;
    }
}
