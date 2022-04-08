<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'device_type', 'device_token', 'deviceable_id', 'deviceable_type', 'status'
    ];

    /**
     * Get the owning deviceable model.
     */
    public function deviceable()
    {
        return $this->morphTo();
    }

    /**
     * Get the device token for use to send push notification
     * 
     * @param $deviceableId user login id
     * @param $userType 2-customer|3-trainer
     * @return object $deviceToken
     */
    public static function getDeviceToken($deviceableId, $userType)
    {
        $deviceableType = ($userType === config('constants.NOTIFICATION_TYPE.CUSTOMER')) ? 'App\Models\Customer' : 'App\Models\Trainer';
        $deviceToken = self::where([
            ['deviceable_id', $deviceableId],
            ['deviceable_type', $deviceableType]
        ])->latest()->first();

        return $deviceToken;
    }
}
