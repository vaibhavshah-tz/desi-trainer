<?php

namespace App\Models;

use CommonHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proposal extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'ticket_id', 'name', 'description', 'quote', 'currency', 'status', 'is_assigned'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleting(function ($proposal) {
            $proposal->trainers()->each(function($trainer) {
                $trainer->delete();
            });
            $proposal->notifications()->delete();
        });
    }

    /**
     * Get the traienrs for the proposal.
     */
    public function trainers()
    {
        return $this->hasMany(ProposalTrainer::class);
    }

    /**
     * Ticket that belongs to the proposal
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * User that belongs to the proposal
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Set the timez value with timezone
     * 
     * @return string
     */
    public function getQuoteLabelAttribute()
    {
        if (!empty($this->attributes['currency']) && !empty($this->attributes['quote'])) {
            return CommonHelper::formatNumber($this->attributes['currency'], $this->attributes['quote']);
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
     * Get all of the proposal notifications.
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notificationtable');
    }
}
