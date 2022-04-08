<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Remove the ticket session value
     */
    public function __construct()
    {
        if (\Session::has('TICKET_ID') && \Request::is('tickets/*')) {
            \Session()::forget('TICKET_ID');
            \Session()::forget('TICKET_PK');
            \Session()::forget('TICKET_STATUS_MESSAGE');
            \Session()::forget('TICKET_DETAIL');
        }
    }
}
