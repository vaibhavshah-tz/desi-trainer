<?php

namespace App\Http\Middleware;

use App\Models\Ticket;
use Closure;

class CheckTicketStatus
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
        if (in_array($ticket->status, [config('constants.TICKET.COMPLETE'), config('constants.TICKET.INACTIVE'), config('constants.TICKET.CANCEL')])) {
            $message = in_array($ticket->status, [config('constants.TICKET.COMPLETE'), config('constants.TICKET.INACTIVE'), config('constants.TICKET.CANCEL')]) ? strtolower($ticket->status_label) : '';
            $request->session()->flash('error', "Can not perform any action as this ticket is " . $message);

            return back();
        }

        return $next($request);
    }
}
