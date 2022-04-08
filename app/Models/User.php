<?php

namespace app\Models;

use App\Models\ChatRoom;
use App\Models\EmailTemplate;
use App\Models\Meeting;
use App\Models\TrainerComment;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletes;
use CommonHelper;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id', 'first_name', 'last_name', 'email', 'email_verified_at',
        'password', 'remember_token', 'status', 'avatar', 'phone_number', 'country_code', 'call_support'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes that are appends to result set.
     *
     * @var array
     */
    protected $appends = ['full_phone_number', 'avatar_url', 'full_name'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleting(function ($user) {
            $user->notifications()->delete();
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
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the avatar image URL
     *
     * @return string image path
     */
    public function getAvatarAttribute()
    {
        return !empty($this->attributes['avatar']) ? config('constants.IMAGE_PATH.AVATAR') . '/' . $this->attributes['avatar'] : '';
    }

    /**
     * Get the avatar image URL
     *
     * @return string image path
     */
    public function getAvatarUrlAttribute()
    {
        $imageUrl = CommonHelper::getImage($this->getAvatarAttribute());
        $image = !empty($imageUrl) ? $imageUrl : config('constants.IMAGE_PATH.DEFAULT_AVATAR');

        return url($image);
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
        $this->attributes['avatar'] = !empty($value) ? CommonHelper::uploadImage(config('constants.IMAGE_PATH.AVATAR'), $value) : null;
    }

    /**
     * Set encrypted password
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        try {
            $user = $this->select('id', 'first_name', 'last_name')->where('email', '=', request()->email)->firstOrFail();
            $emailTemplate = EmailTemplate::select('id', 'subject', 'body')->where('slug', '=', 'admin-reset-password')->firstOrFail();

            $this->notify(new ResetPasswordNotification($token, $emailTemplate, $user));
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * Get the role that owns the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the meetings that owns the tickets.
     */
    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    /**
     * User is associalted with trainer comments
     */
    public function trainerComment()
    {
        return $this->hasMany(TrainerComment::class);
    }

    /**
     * User is associalted with tickets
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * User is associalted with chat room
     */
    public function chatRooms()
    {
        return $this->hasMany(ChatRoom::class);
    }

    /**
     * Get the sub admin list
     * 
     * @return collection array
     */
    public static function getSubAdminList($editTicket = false)
    {
        $condition = [];
        if (!$editTicket) {
            $condition = [['role_id', config('constants.ADMIN_ROLE.SUB_ADMIN')], ['id', '<>', \Auth::id()]];
        } elseif (\Auth::user()->role_id !== config('constants.ADMIN_ROLE.SUPER_ADMIN')) {
            $condition = [['role_id', config('constants.ADMIN_ROLE.SUB_ADMIN')], ['id', '=', \Auth::id()]];
        }

        return self::select('id', \DB::raw("CONCAT(first_name, ' ', last_name) as sub_admin"))
            ->where($condition)
            ->orderBy('sub_admin', 'asc')
            ->pluck('sub_admin', 'id');
    }

    /**
     * Get all meeting notifications.
     */
    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notificationtable');
    }

    /**
     * Get the admin data by id
     * 
     * @param $id
     * @return object
     */
    public static function getUser($id = '')
    {
        $id = !empty($id) ? $id : config('constants.ADMIN_ROLE.SUPER_ADMIN');

        return self::active()->findorfail($id);
    }

    /**
     * Get the all admin data
     * 
     * @return object
     */
    public static function getAllUser()
    {
        return self::select('id', 'status')->active()->get();
    }

    /**
     * Get the admin data
     */
    public static function getCallAdmin()
    {
        // return config('constants.CALL_SUPPORT');
        return self::active()
            ->select('id', 'first_name', 'last_name', 'country_code', 'phone_number', 'call_support')
            ->where('call_support', config('constants.CALL_SUPPORT'))
            ->first();
    }
}
