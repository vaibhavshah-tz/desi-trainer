<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\CommonHelper;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UserLogin;
use App\Http\Requests\StoreCustomer;
use App\Models\Customer;
use App\Models\User;
use App\Traits\UserAuthentication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Events\SendNotification;

class CustomerController extends ApiController
{
    use UserAuthentication;

    /**
     * Customer login
     * 
     * @param \App\Http\Requests\UserLogin $request
     * @return JsonResponse
     */
    public function login(UserLogin $request)
    {
        try {
            $customer = Customer::where('email', $request->email)->first();

            if (!$customer) {
                return $this->apiResponse->respondUnauthorized(__("We did not find a user with this email"));
            }

            if (!Hash::check($request->password, $customer->password)) {
                return $this->apiResponse->respondUnauthorized(__("Invalid password"));
            }

            if (!$customer->status) {
                return $this->apiResponse->respondWithError(__("Your account is not active, Please contact to admin!"));
            }

            if (!$customer->is_otp_verified) {
                // send otp
                if (!$this->sendOtp($customer)) {
                    return $this->apiResponse->respondWithError(__("Can not send otp, Please try again!"));
                }

                return $this->apiResponse->respondWithError(__("Otp is not verified, Please verify!"), null, ['user_id' => $customer->id, 'phone_number' => $customer->fullPhoneNumber]);
            }

            $customer->api_token = CommonHelper::generateApiToken();
            $customer->save();
            
            if (!empty($request->device_token)) {
                $customer->devices()->create($request->only(['device_token', 'device_type']));
            }

            $payload = [
                'token_type' => 'Bearer',
                'customer' => $customer,
                'call_admin' => User::getCallAdmin()
            ];

            return $this->apiResponse->respondWithMessageAndPayload($payload, __("Logged in successfully"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Customer Registeration
     * 
     * @param \App\Http\Requests\StoreCustomer $request
     * @return JsonResponse
     */
    public function register(StoreCustomer $request)
    {
        try {
            $customer = Customer::create($request->all());
            if ($customer) {
                if (!$this->sendOtp($customer)) {
                    return $this->apiResponse->respondWithError(__("Can not send otp, Please try again!"));
                }
                // Send the notification
                $notificationData = [
                    'sender_id' => $customer->id,
                    'receiver_id' => config('constants.ADMIN_ROLE.SUPER_ADMIN'),
                    'sender_type' => config('constants.NOTIFICATION_TYPE.CUSTOMER'),
                    'receiver_type' => config('constants.NOTIFICATION_TYPE.USER'),
                    'title' => 'New customer register',
                    'message' => 'New customer ' . $customer->full_name . ' has been registed.',
                    'redirection_type' => config('constants.NOTIFICATION_REDIRECTION_TYPE.CUSTOMER'),
                ];
                event(new SendNotification($customer, $notificationData));
                return $this->apiResponse->respondWithMessageAndPayload($customer, __("We've sent the OTP to your register phone number, Please verify"));
            }

            return $this->apiResponse->respondWithError(__("Something went wrong!"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Authentication required
     * Update the new password
     * URL - v1/customer/change-password
     * 
     * @param \App\Http\Requests\ChangePasswordRequest $request
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $customer = Auth::guard('customer')->user();
            if (Hash::check($request->old_password, $customer->password)) {
                $customer->password = $request->new_password;
                if ($customer->save()) {
                    return $this->apiResponse->respondWithMessageAndPayload($customer, __("New password updated successfully"));
                }
                return $this->apiResponse->respondWithError(__("Something went wrong!"));
            }

            return $this->apiResponse->respondWithError(__("Old password does not match!"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Customer logout
     * 
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $customer = Auth::guard('customer')->user();
            $customer->api_token = null;
            $customer->save();

            $customer->devices()->delete();
            return $this->apiResponse->respondWithMessage(__("Logged out successfully"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     *  Customer update profile api
     *  URL - /v1/customer/update-profile
     * 
     * @param \App\Http\Requests\StoreCustomer $request
     * @return JsonResponse
     */
    public function updateProfile(StoreCustomer $request)
    {
        try {
            $customer = Customer::findorfail(Auth::guard('customer')->user()->id);
            if ($customer->update($request->all())) {
                $payload = [
                    // 'token_type' => 'Bearer',
                    'customer' => $customer
                ];
                return $this->apiResponse->respondWithMessageAndPayload($payload, __("Profile has been updated successfully."));
            }

            return $this->apiResponse->respondWithError(__("Record not found!"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }
}
