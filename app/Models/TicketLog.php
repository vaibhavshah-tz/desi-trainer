<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TicketLog extends Model
{
    protected $fillable = [
        'ticket_id', 'ticket_log_id', 'ticket_log_type', 'message', 'payload'
    ];

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
    public function ticket_log()
    {
        return $this->morphTo();
    }
}
