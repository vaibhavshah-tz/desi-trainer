<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Http\Response\ApiResponse;

class CheckActiveUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $apiResponse = new ApiResponse();
        $guard = Auth::getDefaultDriver();

        if (Auth::guard($guard)->check()) {
            if (Auth::guard($guard)->user()->status !== config('constants.ACTIVE')) {
                $payload = [
                    'id' => Auth::guard($guard)->user()->id,
                    'verification_status' => Auth::guard($guard)->user()->status,
                ];
                // respondWithMessageAndPayload  respondCreatedWithPayload
                return $apiResponse->respondWithMessageAndPayload($payload, __("User is not verify, Please contact to admin"));
            }
        }

        return $next($request);
    }
}
