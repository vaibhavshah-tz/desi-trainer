<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Requests\ResendOtp;
use App\Http\Requests\CheckEmailRequest;
use App\Http\Requests\CheckPhoneNumberRequest;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\VerifyOtp;
use App\Http\Resources\BottomNavigationCountResource;
use App\Models\Country;
use App\Models\Course;
use App\Models\Customer;
use App\Models\Timezone;
use App\Models\TicketType;
use App\Models\Trainer;
use App\Models\CourseCategory;
use App\Models\PrimarySkill;
use App\Models\ProposalTrainer;
use App\Models\User;
use App\Models\Ticket;
use App\Traits\UserAuthentication;
use Carbon\Carbon;
use CommonHelper;
use Illuminate\Support\Facades\Auth;

class CommonApiController extends ApiController
{
    use UserAuthentication;

    /**
     * Get all country name with id
     * URL - /v1/country-list
     * 
     * @return country list data
     */
    public function getCountry()
    {
        try {
            $countryList = Country::getAllCountry();

            return $this->apiResponse->respondWithMessageAndPayload($countryList);
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Get all primary skill name with id
     * URL - /v1/get-primary-skill
     * 
     * @return primary skill list data
     */
    public function getPrimarySkills()
    {
        try {
            $primarySkillList = PrimarySkill::getAllPrimarySkill();

            return $this->apiResponse->respondWithMessageAndPayload($primarySkillList);
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Get all timezone name with id
     * URL - /v1/timezone-list
     * 
     * @return timezone list data
     */
    public function getTimezone()
    {
        try {
            $timezoneList = Timezone::getAllTimezone();

            return $this->apiResponse->respondWithMessageAndPayload($timezoneList);
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Get all ticket type with id, name, image
     * URL - /v1/ticket-type-list
     * 
     * @return Ticket type data
     */
    public function getTicketType()
    {
        try {
            $ticketTypeList = TicketType::getAllTicketType();

            return $this->apiResponse->respondWithMessageAndPayload($ticketTypeList);
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Get all ticket type with id, name, image
     * URL - /v1/ticket-type-list
     * 
     * @return Ticket type data
     */
    public function getCountryTimezoneTicketType()
    {
        try {
            $data = [];
            $data['country'] = Country::getAllCountry();
            $data['timezone'] = Timezone::getAllTimezone();
            $data['ticket-type'] = TicketType::getAllTicketType();

            return $this->apiResponse->respondWithMessageAndPayload($data);
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Resend otp
     * 
     * @param \App\Http\Requests\ResendOtp $request
     * @return JsonResponse
     */
    public function resendOtp(ResendOtp $request)
    {
        try {
            $user = $this->getUser($request);

            if ($user) {
                if (!$this->sendOtp($user)) {
                    return $this->apiResponse->respondWithError(__("Can not send otp, Please try again!"));
                }
                return $this->apiResponse->respondWithMessageAndPayload($user, "We've sent the OTP to your register phone number, Please verify");
                // return $this->apiResponse->respondWithMessage(__("We've sent the OTP to your register phone number, Please verify"));
            }

            return $this->apiResponse->respondWithError(__("Something went wrong!"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Forget password with email
     * URL - /v1/forget-password
     * 
     * @param \App\Http\Requests\ForgetPasswordRequest $request
     * @return JsonResponse
     */
    public function forgetPassword(ForgetPasswordRequest $request)
    {
        try {
            $user = '';
            switch ($request->user_type) {
                case '1':
                    $user = Customer::where('email', $request->email)->firstOrFail();
                    break;
                case '2':
                    $user = Trainer::where('email', $request->email)->firstOrFail();
                    break;
            }
            if ($user) {
                if ($user->status == config('constants.INACTIVE')) {
                    return $this->apiResponse->respondWithError(__("Your account is not activated, Please contact to admin."));
                }
                if (!$user->is_otp_verified) {
                    return $this->apiResponse->respondWithError(__("Please verify your OTP first."));
                }
                if ($this->sendOtpToEmail($user)) {
                    $payload = [
                        'user_id' => $user->id
                    ];

                    return $this->apiResponse->respondWithMessageAndPayload($payload, __("We've sent the OTP to your register email ID, Please verify"));
                }
            }

            return $this->apiResponse->respondWithError(__("Record not found!"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Verify email token
     * URL - /v1/verify-email-token
     * 
     * @param \App\Http\Requests\VerifyOtp $request
     * @return JsonResponse
     */
    public function verifyEmailToken(VerifyOtp $request)
    {
        try {
            $user = $this->getUser($request);
            if ($request->otp != $user->reset_password_token) {
                return $this->apiResponse->respondWithError(__("Invalid OTP!"));
            }

            if ($user->reset_token_expired_time < Carbon::now()) {
                return $this->apiResponse->respondWithError(__("OTP has been expired, Please try again!"));
            }
            $user->reset_password_token = null;
            $user->reset_token_expired_time = null;
            if ($user->save()) {
                $payload = [
                    'user_id' => $user->id
                ];
                return $this->apiResponse->respondWithMessageAndPayload($payload, __("OTP verified successfully"));
            }

            return $this->apiResponse->respondWithError(__("Something went wrong!"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Verify otp
     * URL - /v1/verify-otp
     * 
     * @param \App\Http\Requests\VerifyOtp $request
     * @return JsonResponse
     */
    public function verifyOtp(VerifyOtp $request)
    {
        try {
            $user = $this->getUser($request);

            if (!$user) {
                return $this->apiResponse->respondWithError(__("Something went wrong!"));
            }

            if ($request->otp != $user->otp) {
                return $this->apiResponse->respondWithError(__("Invalid OTP!"));
            }

            if ($user->otp_expired_time < Carbon::now()) {
                return $this->apiResponse->respondWithError(__("OTP has been expired, Please resend!"));
            }
            $user->otp = null;
            $user->is_otp_verified = 1;
            $user->api_token = CommonHelper::generateApiToken();
            $user->save();

            if (!empty($request->device_token)) {
                $user->devices()->create($request->only(['device_token', 'device_type']));
            }

            $payload = [
                'token_type' => 'Bearer',
                'user' => $user,
                'call_admin' => User::getCallAdmin()
            ];

            return $this->apiResponse->respondWithMessageAndPayload($payload, __("OTP verified successfully"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /** 
     * Get all course category
     * URL - /v1/course-category
     * 
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function getCourseCategory(Request $request)
    {
        try {
            $courseCategoryList = CourseCategory::getAllCourseCategory();
            if (!empty($request->has_other)) {
                array_push($courseCategoryList, ['id' => 0, 'name' => 'Other']);
            }

            return $this->apiResponse->respondWithMessageAndPayload($courseCategoryList);
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Reset password required
     * Update the new password
     * URL - {{local}}/v1/trainer/reset-password
     * 
     * @param \App\Http\Requests\ForgetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $user = $this->getUser($request);
            if ($user) {
                $user->password = $request->new_password;
                if ($user->save()) {
                    return $this->apiResponse->respondWithMessageAndPayload($user, __("New password updated successfully"));
                }
            }

            return $this->apiResponse->respondWithError(__("Something went wrong!"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Get all primary skill based on course category ID
     * URL - /v1/primary-skill/{course_category_id}
     * 
     * @param $courseCategoryId
     * @return JsonResponse
     */
    public function getPrimarySkill($courseCategoryId)
    {
        try {
            $courseCategoryList = PrimarySkill::getSkillByCourseCategory($courseCategoryId);

            return $this->apiResponse->respondWithMessageAndPayload($courseCategoryList);
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Get user
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function getUser(Request $request)
    {
        try {
            $user = '';
            switch ($request->user_type) {
                case '1':
                    $user = Customer::findOrFail($request->user_id);
                    break;
                case '2':
                    $user = Trainer::findOrFail($request->user_id);
                    break;
            }

            return $user;
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * Check unique email
     * 
     * @param \Illuminate\Http\CheckEmailRequest $request
     * @return JsonResponse
     */
    public function checkEmail(CheckEmailRequest $request)
    {
        try {
            return $this->apiResponse->respondWithMessage();
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Get all course based on course category ID
     * URL - /v1/course-list/{course_category_id}
     * 
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function getCourse(Request $request)
    {
        try {
            $course = Course::getCourse($request->id);
            if (!empty($request->has_other)) {
                array_push($course, ['id' => 0, 'name' => 'Other']);
            }

            return $this->apiResponse->respondWithMessageAndPayload($course);
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Get all course
     * URL - /v1/all-course-list
     * 
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function getAllCourses(Request $request)
    {
        try {
            $course = Course::getAllCourses();

            return $this->apiResponse->respondWithMessageAndPayload($course);
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Check valid phone number
     * 
     * @param \Illuminate\Http\CheckPhoneNumberRequest $request
     * @return JsonResponse
     */
    public function checkPhoneNumber(CheckPhoneNumberRequest $request)
    {
        try {
            return $this->apiResponse->respondWithMessage();
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Get the unread count for assign ticket, proposal, interested trainer
     * URL - {{local}}/v1/trainer/bottom-navigation-count
     * Method - GET
     * 
     * @param \App\Http\Requests\StoreCustomer $request
     * @return JsonResponse
     */
    public function bottomNavigationCount()
    {
        try {
            $count = [];
            $now = Carbon::now()->toDateString();
            $count['count'] = 0;
            $isRead = ['is_read', config('constants.UNREAD')];
            if (Auth::guard('trainer')->check()) {
                $counts = Trainer::select(
                    "id",
                )
                    ->withCount(['proposalTrainer as proposal_count' => function ($q) use ($isRead) {
                        $q->where([$isRead]);
                    }])
                    ->withCount(['interestedTickets as interested_ticket_count' => function ($q) use ($isRead) {
                        $q->where('interested_ticket_trainer.is_read', config('constants.UNREAD'));
                    }])
                    ->withCount(['tickets as assign_ticket_count' => function ($q) use ($isRead) {
                        $q->where([$isRead]);
                    }])
                    ->find(Auth::guard('trainer')->user()->id);

                $globalCount = Ticket::select('id', 'created_at', 'is_global')
                    ->doesntHave('interestedTrainers', 'or', function ($q) {
                        $q->where('trainer_id', '=', \Auth::guard('trainer')->user()->id);
                    })
                    ->whereDate('created_at', '=', $now)
                    ->where('is_global', config('constants.TICKET.IS_GLOBAL_YES'))
                    ->whereNull('tickets.trainer_id')
                    ->whereNOTIn('tickets.status', [config('constants.TICKET.COMPLETE'), config('constants.TICKET.INACTIVE'), config('constants.TICKET.CANCEL')])
                    ->count();

                $counts->global_request_count = $globalCount ?? 0;
                $counts = new BottomNavigationCountResource($counts);

                return $this->apiResponse->respondWithMessageAndPayload($counts, __("Bottom navigartion un-read count"));
            }

            return $this->apiResponse->respondWithError(__("Please login with trainer"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Update the bottom navigation count
     * Update the ticket, proposal, interested trainer based on update type
     * URL - {{local}}/v1/trainer/bottom-navigation-count
     * Method - PUT|PATCH
     * 
     * @param \App\Http\Requests\StoreCustomer $request
     * @return JsonResponse
     */
    public function bottomNavigationReadCount(Request $request)
    {
        try {
            $updateStatus = [];
            if (Auth::guard('trainer')->check()) {
                $trainerId = Auth::guard('trainer')->user()->id;
                $ticketId = $request->ticket_id;
                $ticketDetails = Ticket::select('id')->find($ticketId);
                $updateStatus = ['is_read' => config('constants.READ')];
                switch ($request->update_type) {
                    case 1:
                        $updateIsRead = ProposalTrainer::where('trainer_id', '=', $trainerId)->update($updateStatus);
                        break;
                    case 2:
                        $updateIsRead = Trainer::find($trainerId)->interestedTickets()->updateExistingPivot($ticketDetails, $updateStatus, false);
                        break;
                    case 3:
                        $updateIsRead = Ticket::where([
                            ['trainer_id', '=', $trainerId],
                            ['id', '=', $ticketId],
                        ])->update($updateStatus);
                        break;

                    default:
                        return $this->apiResponse->respondWithError(__("Please enter update type and ticket id"));
                        break;
                }
                return $this->bottomNavigationCount();
                // return $this->apiResponse->respondWithMessageAndPayload($data, __("Request mark as read successfully"));
            }

            return $this->apiResponse->respondWithError(__("Please login with trainer"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }
}
