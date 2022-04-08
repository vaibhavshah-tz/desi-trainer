<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Trainer;
use App\Models\Ticket;
use CommonHelper;
use Illuminate\Http\Request;
use Nexmo;
use Carbon\Carbon;

class CallController extends Controller
{
    
    /**
     * Get user info for call
     * 
     * @param \Illuminate\Http\Request $request
     * @return JSON
     */
    public function callInfo(Request $request)
    {
        $user = $this->getUser($request);
        if(!empty($user)) {
            if(is_array($user)) {
                $number = str_replace('+', '', [$user['customer']->full_phone_number, $user['trainer']->full_phone_number]);

                return response()->json(['status' => 1, 'name' => 'Conference', 'number' => implode(',',$number)]);
            }
            $number = str_replace('+', '', $user->full_phone_number);

            return response()->json(['status' => 1, 'name' => $user->full_name, 'number' => $number]);
        }

        return response()->json(['status' => 0]);
    }

    /**
     * Get jwt token
     * 
     * @param \Illuminate\Http\Request $request
     * @return JSON
     */
    public function getToken(Request $request) {
        $expireDate = Carbon::now()->addYear();
        $claims = [
            'sub' => "desitrainer",
            'exp' => $expireDate->timestamp,
            'acl' => [
                'paths' => [
                    '/*/users/**' => (object) [],
                    '/*/conversations/**' => (object) [],
                    '/*/sessions/**' => (object) [],
                    '/*/devices/**' => (object) [],
                    '/*/image/**' => (object) [],
                    '/*/media/**' => (object) [],
                    '/*/applications/**' => (object) [],
                    '/*/push/**' => (object) [],
                    '/*/knocking/**' => (object) [],
                ]
            ]
        ];
        try {
            $jwt = Nexmo::generateJwt($claims);
            $tokenString = (string) $jwt;
    
            return response()->json(['status' => 1, 'token' => $tokenString]);
        } catch (\Exception $ex) {
            return response()->json(['status' => 0]);
        }
        
    }

    /**
     * Answer url
     * 
     * @param \Illuminate\Http\Request $request
     * @return JSON
     */
    public function answer(Request $request)
    {
        //12012874951
        $numbers = explode(',',$request->get("to"));
        $ncco = [];
        foreach($numbers as $number) {
           $connect = [
                    "action" => "connect",
                    "timeout" => 30,
                    // "from" => $request->get("from"),
                    "endpoint" => [
                        [
                            "type" => "phone",
                            "number" => trim($number)
                        ]
                    ]
                ];
            array_push($ncco,$connect);
        }

        return response()->json($ncco);
    }

    /**
     * Event url
     * 
     * @param \Illuminate\Http\Request $request
     * @return JSON
     */
    public function event(Request $request)
    {
        return response()->json([],200);
    }

    /**
     * Get user info
     * 
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function getUser(Request $request) {
        try {
            $user = [];
            switch($request->type) {
                case '1':
                    $user = Customer::select(['id', 'first_name', 'last_name', 'country_code', 'phone_number'])->findOrFail($request->id);
                    break;
                case '2':
                    $user = Trainer::select(['id', 'first_name', 'last_name', 'country_code', 'phone_number'])->findOrFail($request->id);
                    break;
                case '3':
                    $ticket = Ticket::select(['id', 'customer_id', 'trainer_id'])->findOrFail($request->id);
                    if(!$ticket->trainer_id) {
                        return false;
                    }
                    $user['customer'] = $ticket->customer;
                    $user['trainer'] = $ticket->trainer;
                    break;
                case '4':
                    $ticket = Ticket::select(['id', 'customer_id'])->findOrFail($request->id);
                    $user['trainer'] = Trainer::select(['id', 'first_name', 'last_name', 'country_code', 'phone_number'])->findOrFail($request->trainer_id);
                    $user['customer'] = $ticket->customer;
                    break;
            }

            return $user;
        } catch(\Exception $ex) {
            return false;
        }
    }
}
