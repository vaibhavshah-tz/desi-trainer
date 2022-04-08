<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\UserLogin;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\UserAuthentication;
use CommonHelper;
use App\Http\Requests\TrainerRegistrationRequest;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Auth;
use App\Events\SendNotification;
use App\Models\User;

class TrainerController extends ApiController
{
    use UserAuthentication;

    /**
     * Login APIs
     * URLL - /v1/trainer/login
     * 
     * @param \App\Http\Requests\UserLogin $request
     * @return JsonResponse
     */
    public function login(UserLogin $request)
    {
        try {
            $trainer = Trainer::with(['ticketTypes:id,name,image,status', 'primarySkills:id,course_category_id,name,status'])->where('email', $request->email)->first();

            if (!$trainer) {
                return $this->apiResponse->respondUnauthorized(__("We did not find a user with this email"));
            }

            if (!Hash::check($request->password, $trainer->password)) {
                return $this->apiResponse->respondUnauthorized(__("Invalid password"));
            }

            if (!$trainer->status) {
                return $this->apiResponse->respondWithError(__("Your account is not active, Please contact to admin!"));
            }

            if (!$trainer->is_otp_verified) {
                // send otp
                if (!$this->sendOtp($trainer)) {
                    return $this->apiResponse->respondWithError(__("Can not send otp, Please try again!"));
                }

                return $this->apiResponse->respondWithError(__("Otp is not verified, Please verify!"), null, ['user_id' => $trainer->id, 'phone_number' => $trainer->fullPhoneNumber]);
            }
            $trainer->api_token = CommonHelper::generateApiToken();
            $trainer->save();

            if (!empty($request->device_token)) {
                $trainer->devices()->create($request->only(['device_token', 'device_type']));
            }

            $payload = [
                'token_type' => 'Bearer',
                'trainer' => $trainer,
                'call_admin' => User::getCallAdmin()
            ];

            return $this->apiResponse->respondWithMessageAndPayload($payload, __("Logged in successfully"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Trainer registration api
     *  URL - /v1/trainer/registration
     * 
     * @param \App\Http\Requests\TrainerRegistrationRequest $request
     * @return JsonResponse
     */
    public function registration(TrainerRegistrationRequest $request)
    {
        try {
            $user = User::getAllUser();
            $trainer = Trainer::create($request->all());
            if ($trainer) {
                $trainer->saveTicketType($trainer, $request->ticket_type);
                $trainer->savePrimarySkill($trainer, $request->primary_skill);
                if (!$this->sendOtp($trainer)) {
                    return $this->apiResponse->respondWithError(__("Can not send otp, Please try again!"));
                }
                // Send the notification to sub/super admin
                foreach ($user as $key => $value) {
                    $notificationData = [
                        'sender_id' => $trainer->id,
                        'receiver_id' => $value->id,
                        'sender_type' => config('constants.NOTIFICATION_TYPE.TRAINER'),
                        'receiver_type' => config('constants.NOTIFICATION_TYPE.USER'),
                        'title' => 'New trainer register',
                        'message' => 'New trainer ' . $trainer->full_name . ' has been registed.',
                        'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.TRAINER'),
                    ];
                    event(new SendNotification($trainer, $notificationData));
                }

                return $this->apiResponse->respondWithMessageAndPayload($trainer, __("We've send the OTP to your register phone number, Please verify"));
            }

            return $this->apiResponse->respondWithError(__("Something went wrong!"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Authentication required
     * Update the new password
     * URL - /v1/trainer/change-password
     * 
     * @param \App\Http\Requests\ChangePasswordRequest $request
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $trainer = Auth::guard('trainer')->user();
            if (Hash::check($request->old_password, $trainer->password)) {
                $trainer->password = $request->new_password;
                if ($trainer->save()) {
                    return $this->apiResponse->respondWithMessageAndPayload($trainer, __("New password updated successfully"));
                }
                return $this->apiResponse->respondWithError(__("Something went wrong!"));
            }

            return $this->apiResponse->respondUnauthorized(__("Old password does not match!"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Trainer logout
     * URL - /v1/trainer/logout
     * 
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $trainer = Auth::guard('trainer')->user();
            $trainer->api_token = null;
            $trainer->save();

            $trainer->devices()->delete();

            return $this->apiResponse->respondWithMessage(__("Logged out successfully"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Trainer update profile api
     *  URL - /v1/trainer/update-profile
     * 
     * @param \App\Http\Requests\TrainerRegistrationRequest $request
     * @return JsonResponse
     */
    public function updateProfile(TrainerRegistrationRequest $request)
    {
        try {
            $trainer = Trainer::with(['ticketTypes:id,name,image,status', 'primarySkills:id,course_category_id,name,status'])->findorfail(Auth::guard('trainer')->user()->id);
            $trainer->saveTicketType($trainer, $request->ticket_type);
            $trainer->savePrimarySkill($trainer, $request->primary_skill);
            if ($trainer->update($request->all())) {
                return $this->apiResponse->respondWithMessageAndPayload($trainer, __("Profile has been updated successfully."));
            }

            return $this->apiResponse->respondWithError(__("Record not found!"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }
}
