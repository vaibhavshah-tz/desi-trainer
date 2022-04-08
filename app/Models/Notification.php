<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Notification extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'notification_id', 'notification_type', 'sender_id', 'receiver_id', 'sender_type', 'receiver_type',
        'title', 'message', 'is_read', 'redirection_type', 'push_notification'
    ];

    /**
     * The attributes that are appends to result set.
     *
     * @var array
     */
    protected $appends = ['ticket_id', 'ticket_status', 'ticket_status_label', 'redirection_id', 'course_name'];

    /**
     * hide the notification object
     * 
     * @var array
     */
    protected $hidden = ['notificationtable'];

    /**
     * Get human redable date
     */
    public function getCreatedAtAttribute()
    {
        $date = '';
        if (!empty($this->attributes['created_at'])) {
            $date = Carbon::parse($this->attributes['created_at'])->diffForHumans();
        }

        return $date;
    }

    /**
     * Get the owning commentable model.
     */
    public function notificationtable()
    {
        return $this->morphTo();
    }

    /**
     * Set the redirection id
     * 
     * @return int redirection PK
     */
    public function getRedirectionIdAttribute()
    {
        $data =  $this->getTicketDataId();
        $redirectionId = $this->attributes['redirection_type'];
        if ($redirectionId == config('constants.NOTIFICATION_REDIRECTION_TYPE.MEETING') || $redirectionId == config('constants.NOTIFICATION_REDIRECTION_TYPE.CHAT')) {
            return $data->id;
        }

        return $this->notificationtable_id;
    }

    /**
     * Get the ticket model data
     * Check the relation beetween base model and ticket model
     * 
     * @return $ticketDetails object
     */
    public function getTicketDataId()
    {
        $ticketDetails = '';
        $modelType = $this->attributes['notificationtable_type'];

        if ($modelType === 'App\Models\Ticket') {
            $ticketDetails = optional($this->notificationtable);
        } else {
            $ticketDetails = optional($this->notificationtable)->ticket;
        }

        return $ticketDetails;
    }

    /**
     * Get the ticket id - TK
     * 
     * @return string Ticket id Ex. TK-1233
     */
    public function getTicketIdAttribute()
    {
        $data =  $this->getTicketDataId();

        return !empty($data) ? $data->ticket_id : '';
    }

    /**
     * Get the course name if available other wise get the ticket other course name
     * 
     * @return string
     */
    public function getCourseNameAttribute()
    {
        $data =  $this->getTicketDataId();
        $courseName = '';

        $courseName = !empty($data->course) ? $data->course->name : '';
        $courseName = (empty($courseName) && !empty($data->other_course)) ? $data->other_course : $courseName;

        return $courseName;
    }

    /**
     * Get the ticket status
     * 
     * @return int ticket status
     */
    public function getTicketStatusAttribute()
    {
        $data =  $this->getTicketDataId();

        return !empty($data) ? $data->status : '';
    }

    /**
     * Get the ticket status label
     * 
     * @return string ticket status label
     */
    public function getTicketStatusLabelAttribute()
    {
        $data =  $this->getTicketDataId();

        return !empty($data) ? $data->status_label : '';
    }

    /**
     * Get the use notifications
     */
    public static function getNotifications()
    {
        return self::with('notificationtable')
            ->where([['receiver_type', config('constants.NOTIFICATION_TYPE.USER')]])
            ->orderBy('created_at', 'desc');
    }
}
