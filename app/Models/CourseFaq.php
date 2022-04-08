<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseFaq extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'course_id', 'title', 'description'
    ];

    /**
     * Get the course that owns the course faqs.
     */
    public function course()
    {
        return $this->belongsTo(course::class);
    }
}
