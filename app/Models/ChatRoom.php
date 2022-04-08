<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ticket_id', 'user_id', 'customer_id', 'trainer_id', 'room_id', 'status'
    ];

    public static function getRoomId()
    {
        $roomId = uniqid();
        $data = self::where('room_id', $roomId)->first();
        if ($data) {
            self::getRoomId();
        }

        return $roomId;
    }

    /**
     * Chat room belongs to tickets
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Chat room belongs to user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Chat room belongs to customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Chat room belongs to trainer
     */
    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    /**
     * Chat room is associated with message
     */
    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Get all meeting notifications.
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notificationtable');
    }


    /**
     * Set the channel names
     */
    // public function getChannelNameAttribute()
    // {
    //     return 'channel-' . $this->attributes['id'] . '-' . $this->attributes['room_id'];
    // }
}
