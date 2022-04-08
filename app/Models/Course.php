<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\CommonHelper;

class Course extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'course_category_id', 'name', 'description', 'course_price', 'course_special_price',
        'cover_image', 'status', 'trending', 'currency', 'trainer_id'
    ];

    /**
     * The attributes that are appends to result set.
     *
     * @var array
     */
    protected $appends = ['discount', 'cover_image_url', 'price_with_currency', 'price_with_currency_label', 'special_price_with_currency', 'special_price_with_currency_label'];

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
     * Get the course curriculumn for the course.
     */
    public function courseCurriculumns()
    {
        return $this->hasMany(CourseCurriculumn::class);
    }

    /**
     * Get the course faq for the course.
     */
    public function courseFaqs()
    {
        return $this->hasMany(CourseFaq::class);
    }

    /**
     * Get the course faq for the course.
     */
    public function courseFeatures()
    {
        return $this->hasMany(CourseFeature::class);
    }

    /**
     * Get the course category for the course.
     */
    public function courseCategory()
    {
        return $this->belongsTo(CourseCategory::class);
    }

    /**
     * Get the cover image image URL
     *
     * @return string image path
     */
    public function getCoverImageAttribute()
    {
        return !empty($this->attributes['cover_image']) ? config('constants.IMAGE_PATH.COVER_IMAGE') . '/' . $this->attributes['cover_image'] : '';
    }

    /**
     * Get the cover image URL
     *
     * @return string image path
     */
    public function getCoverImageUrlAttribute()
    {
        $imageUrl = CommonHelper::getImage($this->getCoverImageAttribute());

        return !empty($imageUrl) ? url($imageUrl) : url(config('constants.IMAGE_PATH.DEFAULT_COVER_IMAGE'));
    }

    /**
     * Get the discount
     *
     * @return string discount
     */
    public function getDiscountAttribute()
    {
        if (!empty($this->attributes['course_price']) && !empty($this->attributes['course_special_price'])) {
            $percent = round((($this->attributes['course_price'] - $this->attributes['course_special_price']) * 100) / $this->attributes['course_price'], 2) . '%';

            return $percent;
        }
        return 0;
    }

    /**
     * Upload the cover image
     *
     * @param  string  $value image instase
     * @return string image hase name
     */
    public function setCoverImageAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['cover_image'] = !empty($value) ? CommonHelper::uploadImage(config('constants.IMAGE_PATH.COVER_IMAGE'), $value) : null;
        }
    }

    /**
     * Save multiple course feature
     * 
     * @param $model model instanse
     * @param $featureId feature id array
     * @return bool
     */
    public function saveCourseFeature($model, $featureId = [])
    {
        if ($featureId) {
            $model->courseFeatures()->delete();
            $model->courseFeatures()->createMany($featureId);
        }

        return true;
    }

    /**
     * Save multiple course feature
     * 
     * @param $model model instanse
     * @param $faqId faqId id array
     * @return bool
     */
    public function saveFaqs($model, $faqId = [])
    {
        if ($faqId) {
            $model->courseFaqs()->delete();
            $model->courseFaqs()->createMany($faqId);
        }

        return true;
    }

    /**
     * ticket type is associated with tickets
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get the course data based on course category
     * 
     * @param $courseCategoryId int
     * @return course array
     */
    public static function getCourse($courseCategoryId)
    {
        return  Course::orderBy('name', 'asc')->active()
            ->where('course_category_id', $courseCategoryId)
            ->get()->map->only('id', 'name')->toArray();
    }

    /**
     * Get the all course data
     * 
     * @return course array
     */
    public static function getAllCourses()
    {
        return  self::orderBy('name', 'asc')->active()
            ->get()->map->only('id', 'name')->toArray();
    }

    /**
     * Get the course price with currency
     */
    public function getPriceWithCurrencyAttribute()
    {
        if (!empty($this->attributes['course_price']) && !empty($this->attributes['currency'])) {
            $price =  CommonHelper::formatPrice($this->attributes['course_price']);

            return $this->attributes['currency'] . ' ' . $price;
        }

        return '';
    }

    /**
     * Get the course price with currency
     */
    public function getPriceWithCurrencyLabelAttribute()
    {
        if (!empty($this->attributes['course_price']) && !empty($this->attributes['currency'])) {
            return CommonHelper::formatNumber($this->attributes['currency'], $this->attributes['course_price']);
        }

        return '';
    }

    /**
     * Get the course special price with currency
     */
    public function getSpecialPriceWithCurrencyAttribute()
    {
        if (!empty($this->attributes['course_special_price']) && !empty($this->attributes['currency'])) {
            $price =  CommonHelper::formatPrice($this->attributes['course_special_price']);

            return $this->attributes['currency'] . ' ' . $price;
        }

        return '';
    }

    /**
     * Get the course special price with currency label
     */
    public function getSpecialPriceWithCurrencyLabelAttribute()
    {
        if (!empty($this->attributes['course_special_price']) && !empty($this->attributes['currency'])) {
            return CommonHelper::formatNumber($this->attributes['currency'], $this->attributes['course_special_price']);
        }

        return '';
    }

    /**
     * Set the belongs to trainers
     */
    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }
}
