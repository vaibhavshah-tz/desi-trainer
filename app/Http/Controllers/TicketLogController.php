<?php

namespace App\Http\Controllers;

use App\Models\TicketLog;
use Auth;
use Illuminate\Http\Request;

class TicketLogController extends Controller
{
    /**
     * Show all activity logs
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $page_title = 'Activity Log';
        $activityLogs = TicketLog::where('ticket_id', $request->id)
            ->orderBy('created_at', 'desc');

        $activityLogs = $activityLogs->paginate(10);
        if ($request->ajax()) {
            $view = view('ticketLogs._data', compact('activityLogs'))->render();

            return response()->json(['html' => $view]);
        }
        return view('ticketLogs.index', compact(['page_title', 'activityLogs']));
    }
}
