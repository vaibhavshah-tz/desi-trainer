<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'timezone', 'offset', 'label', 'status'
    ];

    /**
     * Scope a query to only get active customer.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', config('constants.ACTIVE'));
    }

    /**
     * Get the trainer that owns the ticket type.
     */
    public function trainer()
    {
        return $this->belongsToMany(Trainer::class);
    }

    /**
     * Get all country
     * 
     * @return array
     */
    public static function getAllTicketType()
    {
        return self::orderBy('name', 'asc')->active()->get()->map->only('id', 'name', 'image')->toArray();
    }

    /**
     * ticket type is associated with tickets
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
