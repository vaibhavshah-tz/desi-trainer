<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timezone extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'timezone', 'offset', 'label', 'status', 'country_code', 'latitude', 'longitude', 'abbreviation', 'offset_second'
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
     * Get all country
     * 
     * @return array
     */
    public static function getAllTimezone()
    {
        return self::orderBy('label', 'asc')->active()->get()->map->only('id', 'label')->toArray();
    }

    /**
     * Get timezone by id
     * 
     * @param string $id
     * @return string
     */
    public static function getTimezoneById($id = null)
    {
        return self::active()->findOrFail($id);
    }

    /**
     * Get the trainer for the timezone.
     */
    public function trainer()
    {
        return $this->hasMany(Trainer::class);
    }

    /**
     * Get the customer for the timezone.
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Get the meetings that owns the timezone.
     */
    public function meetings()
    {
        return $this->belongsTo(Meeting::class);
    }

    /**
     * Get the tickets for the timezone.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
