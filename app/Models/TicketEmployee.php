<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketEmployee extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ticket_id', 'employee_name', 'country_code', 'phone_number', 'email'
    ];

    /**
     * Get the ticket that owns employees.
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the phone number with country code
     *
     * @return string
     */
    public function getFullPhoneNumberAttribute()
    {
        return "{$this->country_code}{$this->phone_number}";
    }
}
