<?php

namespace App\Traits;

use App\Helpers\CommonHelper;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgetPasswordMail;

trait UserAuthentication
{

    /**
     * Send otp
     * 
     * @param $user user eloquent object
     * @return boolean
     */
    public function sendOtp($user)
    {
        try {
            $otp = CommonHelper::generateOtp();
            $message = $otp . ' is your ' .config('app.name'). ' OTP. It expires in '. config('constants.OTP_EXPIRED_MINUTES') . ' minutes';
            $sendOtp = CommonHelper::sendSms($user->full_phone_number, $message);
            if(!$sendOtp) {
                return false;
            }
            $user->otp = $otp;
            $user->otp_expired_time = CommonHelper::getOtpExpiredDate();
            $user->is_otp_verified = 0;
            if ($user->save()) {
                return true;
            }

            return false;
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * Send otp
     * 
     * @param $user user eloquent object
     * @return boolean
     */
    public function sendOtpToEmail($user)
    {
        try {
            $otp = CommonHelper::generateOtp();
            $user->reset_password_token = $otp;
            $user->reset_token_expired_time = CommonHelper::getOtpExpiredDate(config('constants.EMAIL_OTP_EXPIRED_MINUTES'));
            if ($user->save()) {
                Mail::to($user->email)->send(new ForgetPasswordMail($user));
                
                return true;
            }

            return  false;
        } catch (\Exception $ex) {
            return false;
        }
    }
}
