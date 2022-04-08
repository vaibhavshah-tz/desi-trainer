<?php

namespace App\Http\Middleware;

use App\Models\Ticket;
use Auth;
use Closure;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CheckTicketAccess
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
        $ticket = Ticket::findOrFail($request->id);
        if(Auth::check() && Auth::user()->role_id == config('constants.ADMIN_ROLE.SUPER_ADMIN')) {
            if(!$request->routeIs('tickets.edit') && !$request->routeIs('tickets.view') && !$request->routeIs('tickets.update') && !$request->routeIs('tickets.delete')) {
                if(empty($ticket->user_id)) {
                    $request->session()->flash('error', "First assign this ticket to self or sub admin.");

                    return back();
                }
            }
        } else {
            if(!$request->routeIs('tickets.view') && $ticket->user_id != Auth::id()) {
                throw new AccessDeniedHttpException;
            }
        }

        return $next($request);
    }
}
