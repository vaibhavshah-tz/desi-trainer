<?php

namespace App\Helpers;

use App\Mail\CommonMail;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Timezone;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Nexmo;
use Illuminate\Support\Facades\Http;

class CommonHelper
{

    /**
     * Generate otp
     * 
     * @return string
     */
    public static function generateOtp()
    {
        $otp = mt_rand(1000, 9999);

        return $otp;
    }

    /**
     * Get the OTP expired time
     * 
     * @return date
     */
    public static function getOtpExpiredDate($minute = '')
    {
        $minute = !empty($minute) ? $minute : config('constants.OTP_EXPIRED_MINUTES');

        return Carbon::now()->addMinutes($minute);
    }

    /**
     * generate api token
     * 
     * @return string
     */
    public static function generateApiToken()
    {
        $token = uniqid(Str::random(80));

        return $token;
    }

    /**
     * Upload the image
     * 
     * @return has image name
     */
    public static function uploadImage($path, $fileContents)
    {
        $uploadImage = Storage::disk(env('STORAGE_DISK'))->put($path, $fileContents);

        return $uploadImage ? $fileContents->hashName() : false;
    }

    /**
     * Get the image URL
     * 
     * @return image URL
     */
    public static function getImage($fileName = '')
    {
        $imageUrl = '';
        if ($fileName) {
            $exists = Storage::disk(env('STORAGE_DISK'))->exists($fileName);
            $imageUrl = ($exists) ? Storage::url($fileName) : false;
        }

        return $imageUrl;
    }

    /**
     * Admin role array
     * 
     * @return array
     */
    public static function getAdminRole()
    {
        return [
            config('constants.ADMIN_ROLE.SUPER_ADMIN') => config('constants.ADMIN_ROLE.SUPER_ADMIN_LABEL'),
            config('constants.ADMIN_ROLE.SUB_ADMIN') => config('constants.ADMIN_ROLE.SUB_ADMIN_LABEL')
        ];
    }

    /**
     * Get status data
     * 
     * @return array
     */
    public static function getStatus()
    {
        return collect([
            config('constants.ACTIVE') => config('constants.ACTIVE_LABEL'),
            config('constants.INACTIVE') => config('constants.INACTIVE_LABEL'),
        ]);
    }

    /**
     * Set the admin role title and label
     * 
     * @param int $roleId
     * @return array
     */
    public static function roleLabel($roleId = null)
    {
        $roleStatus = [
            config('constants.ADMIN_ROLE.SUPER_ADMIN') => [
                "title" => config('constants.ADMIN_ROLE.SUPER_ADMIN_LABEL'),
                "class" => 'label label-info label-inline mr-1 mb-1',
            ],
            config('constants.ADMIN_ROLE.SUB_ADMIN') => [
                "title" => config('constants.ADMIN_ROLE.SUB_ADMIN_LABEL'),
                "class" => 'label label-dark label-inline mr-1 mb-1',
            ]
        ];

        return $roleId ? $roleStatus[$roleId] : $roleStatus;
    }

    /**
     * Set the gender title and label
     * 
     * @param int $gender
     * @return array
     */
    public static function genderLabel($gender = null)
    {
        $genderStatus = [
            config('constants.GENDER.MALE') => [
                "title" => config('constants.GENDER.MALE_LABEL'),
                "class" => 'label label-info label-inline mr-1 mb-1',
            ],
            config('constants.GENDER.FEMALE') => [
                "title" => config('constants.GENDER.FEMALE_LABEL'),
                "class" => 'label label-dark label-inline mr-1 mb-1',
            ]
        ];

        return $gender ? $genderStatus[$gender] : $genderStatus;
    }

    /**
     * Send mail
     * 
     * @param string $to
     * @param string $slug
     * @param array $replaceArr
     * @return boolean
     */
    public static function sendMail(string $to, string $slug, array $replaceArr)
    {
        try {
            $emailTemplate = EmailTemplate::where('slug', '=', $slug)->firstOrFail();
            $emailContent = self::replaceEmailContents($emailTemplate->body, $replaceArr);
            Mail::to($to)->send(new CommonMail($emailContent, $emailTemplate->subject));

            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * Replace email contents
     * 
     * @param string $emailContent
     * @param array $replaceArr
     * @return string
     */
    public static function replaceEmailContents(string $emailContent, array $replaceArr)
    {
        if (!empty($emailContent) && !empty($replaceArr)) {
            foreach ($replaceArr as $key => $value) {
                $emailContent = str_replace('{' . $key . '}', $value, $emailContent);
            }
        }

        return $emailContent;
    }

    /**
     * Get year listing 
     */
    public static function getYearList()
    {
        $year =  range(1, 50);

        return array_combine($year, $year);
    }

    /**
     * Get month listing
     */
    public static function getMonthList()
    {
        $month = range(1, 12);

        return array_combine($month, $month);
    }

    /**
     * Check unique email
     * 
     * @param \Illuminate\Http\Request
     * @return boolean
     */
    public static function checkEmail($model, $request)
    {
        try {
            $search = [];
            if ($request->id) {
                $search[] = ['id', '<>', $request->id];
            }
            $search[] = ['email', '=', trim($request->email)];
            $data = $model::firstWhere($search);
            if (!empty($request->type)) {
                $isAvailable = ($data) ? false : true;
                return response()->json(['valid' => $isAvailable]);
            }

            return ($data) ? 'false' : 'true';
        } catch (\Exception $ex) {
            return 'false';
        }
    }

    /**
     * Get customer type
     */
    public static function getCustomerType()
    {
        return [
            config('constants.CUSTOMER_TYPE.INDIVIDUAL') => config('constants.CUSTOMER_TYPE.INDIVIDUAL_LABEL'),
            config('constants.CUSTOMER_TYPE.EMPLOYER') => config('constants.CUSTOMER_TYPE.EMPLOYER_LABEL'),
        ];
    }

    /**
     * Update the status value using ajax
     * 
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public static function updateStatus($model, $request)
    {
        try {
            if ($request->ajax()) {
                $data = $model::findorfail($request->id);
                $data->status = ($request->status == 'true') ? config('constants.ACTIVE') : config('constants.INACTIVE');
                if ($data->save()) {
                    return true;
                }
                return false;
            }

            return false;
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * Check unique username
     * 
     * @param \Illuminate\Http\Request
     * @return boolean
     */
    public static function checkUsername($model, $request)
    {
        try {
            $search = [];
            if ($request->id) {
                $search[] = ['id', '<>', $request->id];
            }
            $search[] = ['username', '=', trim($request->username)];
            $data = $model::firstWhere($search);
            if (!empty($request->type)) {
                $isAvailable = ($data) ? false : true;
                return response()->json(['valid' => $isAvailable]);
            }

            return ($data) ? 'false' : 'true';
        } catch (\Exception $ex) {
            return 'false';
        }
    }

    public static function getCurrency()
    {
        return collect([
            'INR' => 'INR',
            'USD' => 'USD'
        ]);
    }

    /**
     * generate ticket id
     * 
     * @return string
     */
    public static function generateTicketId($ticketType = null)
    {
        // $ticketId = 'TK-' . abs(crc32(uniqid())); // date('YmdHis'); // uniqid('TK');
        if ($ticketType) {
            static $RECURSIVE_CALL = 0;
            $min = env('TICKET_ID_MIN_NUMERIC', 10000);
            $max = env('TICKET_ID_MAX_NUMERIC', 99999);
            if ($RECURSIVE_CALL > config('constants.MAX_RECURSIVE_CALL')) {
                $min *= 10;
                $max = ($max * 10) + 9;
                self::updateDotEnv('TICKET_ID_MIN_NUMERIC', $min);
                self::updateDotEnv('TICKET_ID_MAX_NUMERIC', $max);
            }
            $ticketId = self::ticketIdPrefix()[$ticketType] . random_int($min, $max);
            if (Ticket::withTrashed()->where('ticket_id', '=', $ticketId)->exists()) {
                $RECURSIVE_CALL++;
                self::generateTicketId($ticketType);
            }

            return $ticketId;
        }

        return true;
    }

    /**
     *  Update env file configuration
     */
    public static function updateDotEnv($key, $newValue, $delim = '')
    {
        $path = base_path('.env');
        // get old value from current env
        $oldValue = env($key);

        // was there any change?
        if ($oldValue === $newValue) {
            return;
        }

        if (file_exists($path)) {
            // replace current value with new value 
            file_put_contents(
                $path,
                str_replace(
                    $key . '=' . $delim . $oldValue . $delim,
                    $key . '=' . $delim . $newValue . $delim,
                    file_get_contents($path)
                )
            );
        }
    }

    /**
     * Get ticket id prefix
     * 
     * @return string
     */
    public static function ticketIdPrefix()
    {
        return [
            '1' => config('constants.TICKET_TYPE.TRAINING'),
            '2' => config('constants.TICKET_TYPE.JOB_SUPPORT'),
            '3' => config('constants.TICKET_TYPE.INTERVIEW_SUPPORT'),
        ];
    }

    /**
     * Delete image
     * 
     * @return string
     */
    public static function deleteImage($fileName = '')
    {
        if ($fileName) {
            Storage::disk(env('STORAGE_DISK'))->delete($fileName);
        }

        return true;
    }

    /**
     * Convert date to specific timezone
     * 
     * @param string $date
     * @param string $timezone
     * @return string
     */
    public static function convertDateToSpecificTimezone($date, $timezone = null)
    {
        $dateTime = Carbon::createFromFormat('Y-m-d H:i:s', $date, 'UTC')->setTimezone($timezone);

        return $dateTime;
    }

    /**
     * Get UTC time of any date
     * 
     * @param string $date
     * @param string $timezone
     * @return string|boolean
     */
    public static function getUtcTime($date = null, $timezone = null)
    {
        if (!empty($date) && !empty($timezone)) {
            $dateTime = Carbon::createFromFormat('Y-m-d H:i:s', $date, $timezone)->setTimezone('UTC');

            return $dateTime;
        }

        return Carbon::now();
    }

    /**
     * Get ticket status label
     */
    public static function getTicketStatus()
    {
        return collect([
            config('constants.TICKET.NEW') => config('constants.TICKET.NEW_LABEL'),
            config('constants.TICKET.PENDING') => config('constants.TICKET.PENDING_LABEL'),
            // config('constants.TICKET.UNASSIGNED') => config('constants.TICKET.UNASSIGNED_LABEL'),
            config('constants.TICKET.IN_PROGRESS') => config('constants.TICKET.IN_PROGRESS_LABEL'),
            // config('constants.TICKET.ASSIGNED') => config('constants.TICKET.ASSIGNED_LABEL'),            
            config('constants.TICKET.INACTIVE') => config('constants.TICKET.INACTIVE_LABEL'),
            config('constants.TICKET.COMPLETE') => config('constants.TICKET.COMPLETE_LABEL'),
            config('constants.TICKET.CANCEL') => config('constants.TICKET.CANCEL_LABEL'),
        ]);
    }

    /**
     * Get ticket status label
     */
    public static function getTicketStatusLabel()
    {
        return [
            config('constants.TICKET.NEW') => [
                'title' => config('constants.TICKET.NEW_LABEL'),
                'class' => 'label-light-primary'
            ],
            config('constants.TICKET.PENDING') => [
                'title' => config('constants.TICKET.PENDING_LABEL'),
                'class' => 'label-light-warning'
            ],
            config('constants.TICKET.IN_PROGRESS') => [
                'title' => config('constants.TICKET.IN_PROGRESS_LABEL'),
                'class' => 'label-light-success'
            ],
            config('constants.TICKET.INACTIVE') => [
                'title' => config('constants.TICKET.INACTIVE_LABEL'),
                'class' => 'label-light-dark'
            ],
            config('constants.TICKET.COMPLETE') => [
                'title' => config('constants.TICKET.COMPLETE_LABEL'),
                'class' => 'label-light-info'
            ],
            config('constants.TICKET.CANCEL') => [
                'title' => config('constants.TICKET.CANCEL_LABEL'),
                'class' => 'label-light-danger'
            ],
        ];
    }

    /**
     * Get create meeting type
     */
    public static function createMeetingType($isTrainerAssigned = false)
    {
        if ($isTrainerAssigned) {
            return collect([
                config('constants.MEETING.CREATE_WITH.CUSTOMER') => config('constants.MEETING.CREATE_WITH.CUSTOMER_LABEL'),
                config('constants.MEETING.CREATE_WITH.ASSIGNED_TRAINER') => config('constants.MEETING.CREATE_WITH.ASSIGNED_TRAINER_LABEL'),
                config('constants.MEETING.CREATE_WITH.CUSTOMER_AND_ASSIGNED_TRAINER') => config('constants.MEETING.CREATE_WITH.CUSTOMER_AND_ASSIGNED_TRAINER_LABEL'),
            ]);
        }

        return collect([
            config('constants.MEETING.CREATE_WITH.CUSTOMER') => config('constants.MEETING.CREATE_WITH.CUSTOMER_LABEL'),
            config('constants.MEETING.CREATE_WITH.INTERESTED_TRAINER') => config('constants.MEETING.CREATE_WITH.INTERESTED_TRAINER_LABEL'),
            config('constants.MEETING.CREATE_WITH.CUSTOMER_AND_INTERESTED_TRAINER') => config('constants.MEETING.CREATE_WITH.CUSTOMER_AND_INTERESTED_TRAINER_LABEL'),
        ]);
    }

    /**
     * Get trainer status data
     * 
     * @return array
     */
    public static function getTrainerStatus()
    {
        return collect([
            config('constants.ACTIVE') => config('constants.ACTIVE_LABEL'),
            config('constants.INACTIVE') => config('constants.INACTIVE_LABEL'),
            config('constants.NOT_VERIFIED') => config('constants.NOT_VERIFIED_LABEL'),
        ]);
    }

    /**
     * Set the trainer status title and label
     * 
     * @param int $status
     * @return array
     */
    public static function trainerStatusLabel($status = null)
    {
        $trainerStatus = [
            config('constants.ACTIVE') => [
                "title" => config('constants.ACTIVE_LABEL'),
                "class" => 'label label-success label-inline mr-1 mb-1',
            ],
            config('constants.INACTIVE') => [
                "title" => config('constants.INACTIVE_LABEL'),
                "class" => 'label label-danger label-inline mr-1 mb-1',
            ],
            config('constants.NOT_VERIFIED') => [
                "title" => config('constants.NOT_VERIFIED_LABEL'),
                "class" => 'label label-warning label-inline mr-1 mb-1',
            ]
        ];

        return $trainerStatus[$status] ?? [];
    }

    /**
     * Get proposal status data
     * 
     * @return array
     */
    public static function getProposalStatus()
    {
        return collect([
            config('constants.PROPOSAL.ACCEPTED') => config('constants.PROPOSAL.ACCEPTED_LABEL'),
            config('constants.PROPOSAL.DENIED') => config('constants.PROPOSAL.DENIED_LABEL'),
            config('constants.PROPOSAL.PENDING') => config('constants.PROPOSAL.PENDING_LABEL'),
        ]);
    }

    /**
     * Set the propoal status title and label
     * 
     * @param int $status
     * @return array
     */
    public static function proposalStatusLabel($status = null)
    {
        $proposalStatus = [
            config('constants.PROPOSAL.ACCEPTED') => [
                "title" => config('constants.PROPOSAL.ACCEPTED_LABEL'),
                "class" => 'label-light-success',
            ],
            config('constants.PROPOSAL.DENIED') => [
                "title" => config('constants.PROPOSAL.DENIED_LABEL'),
                "class" => 'label-light-danger',
            ],
            config('constants.PROPOSAL.PENDING') => [
                "title" => config('constants.PROPOSAL.PENDING_LABEL'),
                "class" => 'label-light-warning',
            ],
        ];

        return $proposalStatus[$status] ?? [];
    }

    /**
     * Set the route for notification
     * 
     * @param $data notification data
     * @return link string
     */
    public static function setRedirectLink($data)
    {
        $route = 'javascript:void(0)';
        if (!empty($data->notificationtable)) {
            switch ($data->redirection_type) {
                case config('constants.NOTIFICATION_REDIRECTION_TYPE.MEETING'):
                    $route = route('meetings', ['id' => $data->notificationtable->ticket_id]);
                    break;
                case config('constants.NOTIFICATION_REDIRECTION_TYPE.CUSTOMER'):
                    $route = route('customer.view', ['id' => $data->notificationtable_id]);
                    break;
                case config('constants.NOTIFICATION_REDIRECTION_TYPE.TRAINER'):
                    $route = route('trainer.view', ['id' => $data->notificationtable_id]);
                    break;
                case config('constants.NOTIFICATION_REDIRECTION_TYPE.TICKET'):
                    $route = route('tickets.view', ['id' => $data->notificationtable_id]);
                    break;
                case config('constants.NOTIFICATION_REDIRECTION_TYPE.PROPOSAL'):
                    $route = route('proposals', ['id' => $data->notificationtable->ticket_id]);
                    break;
                case config('constants.NOTIFICATION_REDIRECTION_TYPE.CUSTOMER_PRICING'):
                    $route = route('tickets.customer.pricing', ['id' => $data->notificationtable->ticket_id]);
                    break;
                case config('constants.NOTIFICATION_REDIRECTION_TYPE.TRAINER_QUOTE'):
                    $route = route('tickets.invoices', ['id' => $data->notificationtable->ticket_id]);
                    break;
                case config('constants.NOTIFICATION_REDIRECTION_TYPE.PROPOSAL_TRAINER'):
                    $route = route('tickets.proposals.view', ['id' => $data->notificationtable->proposal->ticket_id, 'proposal_id' => $data->notificationtable->proposal_id]);
                    break;
                case config('constants.NOTIFICATION_REDIRECTION_TYPE.CUSTOMER_PAYMENT'):
                    $route = route('tickets.invoices.view', ['id' => $data->notificationtable->customerQuote->ticket_id, 'invoice_id' => $data->notificationtable->id]);
                    break;
                case config('constants.NOTIFICATION_REDIRECTION_TYPE.INTERESTED_TICKET'):
                    $route = route('tickets.interested-trainers', ['id' => $data->notificationtable->id]);
                    break;

                default:
                    return $route;
                    break;
            }
        }

        return $route;
    }

    /**
     * Send the mail to super admin when any cron is throw the exception
     * 
     * @param $className string
     * @param $exception string
     * @param $lineNumber int
     * @return bool
     */
    public static function sendCronExceptionMail($className = '', $exception = '', $lineNumber = '')
    {
        $user = User::getUser();
        $replaceArr = [
            'CLASS_NAME' => $className ?? '',
            'EXCEPTION' =>  $exception ?? '',
            'LINE' =>  $lineNumber ?? '',
        ];
        CommonHelper::sendMail($user->email, 'cron-exception', $replaceArr);

        return true;
    }

    /**
     * Set the quote status title and label
     * 
     * @param int $status
     * @return array
     */
    public static function quoteStatusLabel($status = null)
    {
        $quoteStatus = [
            config('constants.PAYMENT.PAID') => [
                "title" => config('constants.PAYMENT.PAID_LABEL'),
                "class" => 'label-light-success',
            ],
            config('constants.PAYMENT.DUE') => [
                "title" => config('constants.PAYMENT.DUE_LABEL'),
                "class" => 'label-light-danger',
            ]
        ];

        return $quoteStatus[$status] ?? [];
    }

    /**
     * Format the price
     * 
     * @param $price price to get formated
     */
    public static function formatPrice($price = '')
    {
        if ($price) {
            return (strpos($price, '.') !== false) ? number_format($price, 2) : number_format($price);
        }

        return '';
    }

    /**
     * Format the number
     * 
     * @param $number number to get formated
     */
    public static function formatNumber($currency = '', $number = '')
    {
        if ($number) {
            $price =  self::formatPrice($number);

            return '<span class="label label-lg font-weight-bold label-light-info label-inline">' . $currency . ' ' . $price . '</span>';
        }

        return '';
    }

    /**
     * Generate invoice number
     */
    public static function generateInvoiceNumber()
    {
        return 'INV-' . time();
    }

    /**
     * Get payment status data
     * 
     * @return array
     */
    public static function getPaymentStatus()
    {
        return collect([
            config('constants.PAYMENT.PAID') => config('constants.PAYMENT.PAID_LABEL'),
            config('constants.PAYMENT.DUE') => config('constants.PAYMENT.DUE_LABEL')
        ]);
    }

    /**
     * Send sms
     */
    public static function sendSms($to = null, $message = null)
    {
        try {
            if (!empty($to) && !empty($message)) {
                $sms = Nexmo::message()->send([
                    'to'   => $to,
                    'from' => config('app.name'),
                    'text' => $message
                ]);
                if ($sms->getStatus() == 0) {
                    return true;
                }

                return false;
            }

            return false;
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * Set the formate time with timezone abbreviation
     * 
     * @param $time
     * @param $timezone array
     * @return time with abbr
     */
    public static function timeWithAbbreviation($time, $timezone)
    {
        $timeWithAbbr = '';
        if (!empty($time)) {
            $zone = !empty($timezone) ? $timezone['timezone'] : 'UTC';
            $abbreviation = !empty($timezone) ? $timezone['abbreviation'] : 'UTC';
            $time = Carbon::parse($time, $zone);
            $timeWithAbbr = $time->format('h:iA ') . $abbreviation;
        }

        return trim($timeWithAbbr);
    }

    /**
     * Get ket of trainer ticket type price
     */
    public static function getTrainerTicketTypeKey()
    {
        return [
            '1' => 'training_price',
            '2' => 'job_support_price',
            '3' => 'interview_support_price',
        ];
    }

    /**
     * Get ket of trainer ticket type price
     */
    public static function dateFilter()
    {
        return collect([
            'current_month' => 'Current Month',
            'last_three_month' => 'Last Three Month',
            'current_year' => 'Current Year',
        ]);
    }

    /**
     * Get exchange rates
     */
    public static function getExchangeRates() {
        try {
            $response = Http::get('https://api.exchangeratesapi.io/latest', [
                'base' => 'USD',
                'symbols' => 'INR',
            ]);
            // $response = Http::get('https://openexchangerates.org/api/latest.json', [
            //     'app_id' => 'e4e3a4be38b640a08e4004b7b07a0e28',
            //     'base' => 'USD',
            //     'symbols' => 'INR',
            // ]);
            
            if($response->ok()) {
                $result = json_decode($response->body());

                return $result;
            }

            return '';
        } catch(\Exception $ex) {
            return '';
        }
    }

    /**
     * Get foreign exchange price rate based on the currency
     */
    public static function foreignExchangePrice($currency, $basePrice) 
    {
        $result = self::getExchangeRates();
        $dollarPrice = 0;
        if($result) {
            $dollarPrice = number_format($result->rates->INR, 2);
        }

        switch($currency) {
            case config('constants.CURRENCY.INR'):
                $price = ($dollarPrice) ? number_format(($basePrice / $dollarPrice), 2, '.', '') : '';
                break;
            case config('constants.CURRENCY.USD'):
                $price = ($dollarPrice) ? number_format(($basePrice * $dollarPrice), 2, '.', '') : '';
                break;
            default:
                $price = $basePrice;
                break;
        }

        return $price;
    }
}
