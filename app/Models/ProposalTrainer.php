<?php

namespace App\Models;

use CommonHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProposalTrainer extends Model
{
    use SoftDeletes;

    private $proposalStatus;
    private $redirectionType = 0;
    private $colorFlag;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'proposal_id', 'trainer_id', 'action', 'denied_reason'
    ];

    /**
     * The attributes that are appends to result set.
     *
     * @var array
     */
    protected $appends = ['action_label', 'proposal_status', 'redirection_type', 'color_flag'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleting(function ($proposalTrainer) {
            $proposalTrainer->notifications()->delete();
        });
    }

    /**
     * Proposal that belongs to the trainer
     */
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    /**
     * Trainer that belongs to the proposal
     */
    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    /**
     * Set the denied reason
     *
     * @param  string  $value
     */
    public function setDeniedReasonAttribute($value)
    {
        $this->attributes['denied_reason'] = ($this->attributes['action'] == config('constants.PROPOSAL.ACCEPTED')) ? null : $value;
    }

    /**
     * Get action label attribute
     * 
     * @return string
     */
    public function getActionLabelAttribute()
    {
        if (isset($this->attributes['action'])) {
            return CommonHelper::proposalStatusLabel($this->attributes['action'])['title'];
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
     * Get all meeting notifications.
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notificationtable');
    }

    /**
     * Set the proposal status based on trainer action and admin assign ticket action
     * 
     * @return string
     */
    public function getProposalStatusAttribute()
    {
        $ticketData = $this->proposal->ticket;
        switch ($this->attributes['action']) {
            case 0:
                $this->proposalStatus = 'New';
                $this->colorFlag = config('constants.MOBILE_COLOR_FLAG.GREEN');
                break;
            case 1:
                $this->proposalStatus = 'Proposal Acceptance Sent (Admin Review in Progress)';
                $this->colorFlag = config('constants.MOBILE_COLOR_FLAG.RED');
                break;
            case 2:
                $this->proposalStatus = 'Denied: ' . $this->attributes['denied_reason'];
                $this->colorFlag = config('constants.MOBILE_COLOR_FLAG.DARK_BLUE');
                break;
        }

        if (\Auth::guard('trainer')->check() && $ticketData) {
            if ($ticketData->trainer_id !== null) {
                if ($ticketData->trainer_id === \Auth::guard('trainer')->user()->id) {
                    $this->proposalStatus = 'You have been assigned to this ticket';
                    $this->redirectionType = config('constants.NOTIFICATION_REDIRECTION_TYPE.ASSIGNED_TICKET');
                    $this->colorFlag = config('constants.MOBILE_COLOR_FLAG.SKY_BLUE');
                } else {
                    $this->proposalStatus = 'This Ticket is No Longer Active';
                    $this->colorFlag = config('constants.MOBILE_COLOR_FLAG.GREY');
                }
            }
            if (in_array($ticketData->status, [config('constants.TICKET.INACTIVE'), config('constants.TICKET.CANCEL')])) {
                $this->proposalStatus =  'This Ticket is No Longer Active';
                $this->colorFlag = config('constants.MOBILE_COLOR_FLAG.GREY');
            }
        }

        return $this->proposalStatus;
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
     * get color flag
     * 
     * @return string
     */
    public function getColorFlagAttribute()
    {
        return $this->colorFlag ?? '';
    }
}
