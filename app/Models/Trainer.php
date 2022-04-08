<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use CommonHelper;

class Trainer extends Authenticatable
{
    use SoftDeletes, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'country_id', 'course_category_id', 'timezone_id', 'first_name', 'last_name', 'gender', 'email', 'password', 'username', 'avatar', 'certificate', 'country_code', 'phone_number', 'city', 'zipcode', 'skill_title', 'total_experience_year', 'total_experience_month', 'prior_teaching_experience_year', 'prior_teaching_experience_month', 'resume', 'otp', 'otp_expired_time',
        'is_otp_verified', 'reset_password_token', 'reset_token_expired_time', 'status', 'remove_avatar', 'training_price', 'job_support_price', 'interview_support_price', 'remove_resume'
    ];

    /**
     * The attributes that should be hidden.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleting(function ($trainer) {
            $trainer->notifications()->delete();
        });
    }

    /**
     * The attributes that are appends to result set.
     *
     * @var array
     */
    protected $appends = ['avatar_url', 'resume_url', 'full_name', 'full_phone_number'];

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

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
     * Upload the avatar image
     *
     * @param  string  $value image instase
     * @return string image hase name
     */
    public function setAvatarAttribute($value)
    {
        if (CommonHelper::getImage($this->getAvatarAttribute())) {
            CommonHelper::deleteImage($this->getAvatarAttribute());
        }
        $this->attributes['avatar'] = CommonHelper::uploadImage(config('constants.IMAGE_PATH.AVATAR'), $value);
    }

    /**
     * Remove the avatar image
     */
    public function setRemoveAvatarAttribute($value)
    {
        if (!empty($value)) {
            if (CommonHelper::getImage($this->getAvatarAttribute())) {
                CommonHelper::deleteImage($this->getAvatarAttribute());
            }
            $this->attributes['avatar'] = null;
        }
    }

    /**
     * Remove the resume image
     */
    public function setRemoveResumeAttribute($value)
    {
        if (!empty($value)) {
            if (CommonHelper::getImage($this->getResumeAttribute())) {
                CommonHelper::deleteImage($this->getResumeAttribute());
            }
            $this->attributes['resume'] = null;
        }
    }

    /**
     * For inactive user remove the api token
     * Logout the logged in user session
     */
    public function setStatusAttribute($value)
    {
        if (isset($value) && $value == config('constants.INACTIVE')) {
            $this->attributes['api_token'] = null;
            $this->devices()->delete();
        }
        $this->attributes['status'] = $value;
    }


    /**
     * Get the avatar image URL
     *
     * @return string image path
     */
    public function getAvatarAttribute()
    {
        return !empty($this->attributes['avatar']) ? config('constants.IMAGE_PATH.AVATAR') . '/' . $this->attributes['avatar'] : null;
    }

    /**
     * Get the avatar image URL
     *
     * @return string image path
     */
    public function getAvatarUrlAttribute()
    {
        $imageUrl = CommonHelper::getImage($this->getAvatarAttribute());

        return !empty($imageUrl) ? url($imageUrl) : url(config('constants.IMAGE_PATH.DEFAULT_AVATAR'));
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

    /**
     * Upload the certificate image
     *
     * @param  string  $value image instase
     * @return string image hase name
     */
    public function setCertificateAttribute($value)
    {
        $this->attributes['certificate'] = CommonHelper::uploadImage(config('constants.IMAGE_PATH.CERTIFICATE'), $value);
    }

    /**
     * Upload the resume image
     *
     * @param  string  $value image instase
     * @return string image hase name
     */
    public function setResumeAttribute($value)
    {
        $this->attributes['resume'] = CommonHelper::uploadImage(config('constants.IMAGE_PATH.RESUME'), $value);
    }

    /**
     * Get the resume URL
     *
     * @return string image path
     */
    public function getResumeAttribute()
    {
        return !empty($this->attributes['resume']) ? config('constants.IMAGE_PATH.RESUME') . '/' . $this->attributes['resume'] : '';
    }

    /**
     * Get the avatar image URL
     *
     * @return string image path
     */
    public function getResumeUrlAttribute()
    {
        $imageUrl = CommonHelper::getImage($this->getResumeAttribute());

        return !empty($imageUrl) ? url($imageUrl) : false;
    }

    /**
     * Bcrypt the password
     *
     * @param  string $value plain text
     * @return string Hash password
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * Get the ticket type for trainer
     */
    public function ticketTypes()
    {
        return $this->belongsToMany(TicketType::class)->withTimestamps();
    }

    /**
     * Get the primary skills for trainer
     */
    public function primarySkills()
    {
        return $this->belongsToMany(PrimarySkill::class)->withTimestamps();
    }

    /**
     * Get the country that owns the trainer.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the timezone that owns the trainer.
     */
    public function timezone()
    {
        return $this->belongsTo(Timezone::class);
    }

    /**
     * Get the course category that owns the trainer.
     */
    public function courseCategory()
    {
        return $this->belongsTo(CourseCategory::class);
    }

    /**
     * The trainers that belong to the ticket.
     */
    public function interestedTickets()
    {
        return $this->belongsToMany(Ticket::class, 'interested_ticket_trainer')
            ->withPivot(['is_read'])
            ->withTimestamps();
    }

    /**
     * Save multiple ticket type
     * 
     * @param $trainer model instanse
     * @param $ticketId ticket id array
     * @return bool
     */
    public function saveTicketType($trainer, $ticketId = [])
    {
        if ($ticketId) {
            $trainer->ticketTypes()->sync($ticketId);
        }

        return true;
    }

    /**
     * Save multiple primary skills
     * 
     * @param $trainer model instanse
     * @param $primarySkill primary skill id array
     * @return bool
     */
    public function savePrimarySkill($trainer, $primarySkill = [])
    {
        // if ($primarySkill) {
        $trainer->primarySkills()->sync($primarySkill);
        // }

        return true;
    }

    /**
     * Get all of the trainer's devices.
     */
    public function devices()
    {
        return $this->morphMany('App\Models\UserDevice', 'deviceable');
    }

    /**
     * trainer is associated with tickets
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get the meetings that owns the trainer.
     */
    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    /**
     * trainer is associated with tickets
     */
    public function trainerComments()
    {
        return $this->hasMany(TrainerComment::class);
    }

    /**
     * Get all meeting notifications.
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notificationtable');
    }

    /**
     * Trainer that belongs to the proposal
     */
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    /**
     * Trainer that belongs to the proposal
     */
    public function proposalTrainer()
    {
        return $this->hasMany(ProposalTrainer::class);
    }


    /**
     * Get the total experience
     *
     * @return string
     */
    public function getTotalExperienceAttribute()
    {
        $years = !empty($this->attributes['total_experience_year']) ? $this->attributes['total_experience_year'] : 0;
        $months = !empty($this->attributes['total_experience_month']) ? '.' . $this->attributes['total_experience_month'] : '';

        return $years . $months . ' yrs';
    }

    /**
     * trainer is associated with quotes
     */
    public function quotes()
    {
        return $this->hasMany(TrainerQuote::class);
    }

    /**
     * User is associalted with chat room
     */
    public function chatRooms()
    {
        return $this->hasMany(ChatRoom::class);
    }

    /**
     * User is associalted with courses
     */
    public function courses()
    {
        return $this->hasOne(Course::class);
    }

    /**
     * Get all trainer active list
     */
    public static function getTrainer()
    {
        return self::active()->get()->pluck('full_name', 'id');
    }
}
