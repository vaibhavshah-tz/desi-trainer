<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CourseCurriculumn extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'course_id', 'parent_id', 'title', 'description'
    ];


    /**
     * Get the course that owns the course curriculumn.
     */
    public function course()
    {
        return $this->belongsTo(course::class);
    }

    /**
     * Set the child relationship
     * Get the all children records
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    /**
     * Set the parent relationship
     * Get the all parent records
     */
    public function parent()
    {
        return $this->hasOne(self::class, 'id')->where('parent_id', 0);
    }

    /**
     * Save multiple course curriculum
     * 
     * @param $courseModel course model instance
     * @param $curriculum course curriculum data
     * @return bool
     */
    public static function saveCourseCurriculum($courseModel, $curriculum)
    {
        if ($courseModel->id) {
            $courseModel->courseCurriculumns()->delete();
            self::whereIn('parent_id', array_keys($curriculum))->delete();
            foreach ($curriculum as $key => $value) {
                $model = new self();
                $model->course_id = $courseModel->id;
                $model->parent_id = 0;
                $model->title = $value['title'];
                $model->description = $value['description'];
                $model->save();
                if (!empty($value['topic'])) {
                    foreach ($value['topic'] as $topicKey => $topicValue) {
                        $value['topic'][$topicKey]['course_id'] = 0;
                        $value['topic'][$topicKey]['parent_id'] = $model->id;
                        $value['topic'][$topicKey]['created_at'] = Carbon::now();
                        $value['topic'][$topicKey]['updated_at'] = Carbon::now();
                    }
                    self::insert($value['topic']);
                }
            }
        }
        return;
    }
}
