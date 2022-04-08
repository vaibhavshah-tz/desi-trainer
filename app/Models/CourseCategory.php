<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseCategory extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'status'
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
     * Get the primary skill for the caourse category.
     */
    public function primarySkill()
    {
        return $this->hasMany(PrimarySkill::class);
    }

    /**
     * Get all course category list
     * 
     * @return array
     */
    public static function getAllCourseCategory()
    {
        return self::orderBy('name', 'asc')->active()->get()->map->only('id', 'name')->toArray();
    }

    /**
     * Get the trainer for the course category.
     */
    public function trainer()
    {
        return $this->hasMany(Trainer::class);
    }

    /**
     * Get the courses for the course category.
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * course category is associated with tickets
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
