<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'phone_code', 'flag', 'sort_code', 'status'
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
    public static function getAllCountry()
    {
        return self::orderBy('name', 'asc')->active()->get()->map->only('id', 'name')->toArray();
    }

    /**
     * Get the trainer for the country.
     */
    public function trainers()
    {
        return $this->hasMany(Trainer::class);
    }

    /**
     * Get the customer for the country.
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Get all country code
     * 
     * @return array
     */
    public static function getAllCountryCode()
    {
        return self::active()->get()->pluck('phone_code', 'phone_code')->sort()->unique();
    }
}
