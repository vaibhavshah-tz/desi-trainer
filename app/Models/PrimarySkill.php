<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrimarySkill extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'course_category_id', 'name', 'status'
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
     * Get the course category that owns the primary skill.
     */
    public function courseCategory()
    {
        return $this->belongsTo(CourseCategory::class);
    }

    /**
     * Get the trainer that owns the ticket type.
     */
    public function trainer()
    {
        return $this->belongsToMany(Trainer::class);
    }

    /**
     * The tickets that belong to the primary skill.
     */
    public function tickets()
    {
        return $this->belongsToMany(Ticket::class)->withTimestamps();
    }

    /**
     * Get all primary skill list
     * 
     * @return array
     */
    public static function getAllPrimarySkill()
    {
        return self::orderBy('name', 'asc')->active()->get()->map->only('id', 'name')->toArray();
    }

    /**
     * Get the all primary skill based on course category ID
     * 
     * @param $courseCategoryId Course category ID
     * @return collection
     */
    public static function getSkillByCourseCategory($courseCategoryId)
    {
        return self::orderBy('name', 'asc')->active()->where('course_category_id', $courseCategoryId)->get()->map->only('id', 'name');
    }
}
