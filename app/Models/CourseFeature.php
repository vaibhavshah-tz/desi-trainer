<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseFeature extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'course_id', 'title'
    ];

    /**
     * Get the course that owns the course feature.
     */
    public function course()
    {
        return $this->belongsTo(course::class);
    }
}
