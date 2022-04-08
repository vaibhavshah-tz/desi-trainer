<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use CommonHelper;

class Customer extends Authenticatable
{
    use SoftDeletes, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'country_id', 'timezone_id', 'first_name', 'last_name', 'email', 'password',
        'gender', 'company_name', 'company_address', 'username', 'avatar', 'user_type',
        'country_code', 'phone_number', 'city', 'zipcode', 'otp', 'otp_expired_time',
        'is_otp_verified', 'reset_password_token', 'reset_token_expired_time', 'status', 'remove_avatar'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'otp_expired_time', 'reset_token_expired_time'
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
     * The attributes that are appends to result set.
     *
     * @var array
     */
    protected $appends = ['avatar_url', 'full_name', 'full_phone_number'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleting(function ($customer) {
            $customer->notifications()->delete();
        });
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
     * Get the customer's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the customer's full name.
     *
     * @return string
     */
    public function getMobileNumberAttribute()
    {
        return "{$this->country_code}{$this->phone_number}";
    }

    /**
     * Set encrypted password
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Get all of the customer's devices.
     */
    public function devices()
    {
        return $this->morphMany('App\Models\UserDevice', 'deviceable');
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

        $this->attributes['avatar'] = !empty($value) ? CommonHelper::uploadImage(config('constants.IMAGE_PATH.AVATAR'), $value) : null;
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
     * Set the company name
     *
     * @param  string  $value
     */
    public function setCompanyNameAttribute($value)
    {
        if (!empty($this->attributes['user_type'])) {
            $this->attributes['company_name'] = ($this->attributes['user_type'] == config('constants.CUSTOMER_TYPE.INDIVIDUAL')) ? null : $value;
        }
    }

    /**
     * Set the company address
     *
     * @param  string  $value
     */
    public function setCompanyAddressAttribute($value)
    {
        $this->attributes['company_address'] = ($this->attributes['user_type'] == config('constants.CUSTOMER_TYPE.INDIVIDUAL')) ? null : $value;
    }

    /**
     * Get the country that owns the customer.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the timezone that owns the customer.
     */
    public function timezone()
    {
        return $this->belongsTo(Timezone::class);
    }

    /**
     * Get the meetings that owns the customer.
     */
    public function meetings()
    {
        return $this->belongsTo(Meeting::class);
    }

    /**
     * Get all meeting notifications.
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notificationtable');
    }

    /**
     * Customer is associated with quotes
     */
    public function quotes()
    {
        return $this->hasMany(CustomerQuote::class);
    }

    /**
     * User is associalted with chat room
     */
    public function chatRooms()
    {
        return $this->hasMany(ChatRoom::class);
    }
}
