<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notification;
use App\Models\Ticket;
use App\Models\Trainer;
use App\Models\TrainerQuote;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private $_adminSearch,
        $_oldDateString,
        $_dateSearch,
        $_startDate,
        $_endDate = null;

    /**
     * Set the value in global variable
     * Set the all search value
     * 
     * @param $request Request
     * @return array
     */
    public function setGlobalSearchValue($request)
    {
        if (Auth::user()->role_id === config('constants.ADMIN_ROLE.SUPER_ADMIN')) {
            $this->_adminSearch = $request->sub_admin ?? null;
        } else {
            $this->_adminSearch = Auth::user()->id ?? null;
        }

        if (!empty($request->start_date)) {
            if (strtotime($request->start_date)) {
                $this->_startDate = $this->searchStringDate($request->start_date) ?? null;
                $this->_endDate = $this->searchStringDate($request->end_date) ?? null;
            } else {
                $this->_oldDateString = $request->start_date ?? null;
                $this->_startDate = $this->searchStringDate($this->_oldDateString) ?? null;
            }
        }
    }

    /**
     * Set the all dashboard data
     * Include the all files for view
     * 
     * @param sub_admin,dates value
     * @param $request
     * @return view
     */
    public function index(Request $request)
    {
        $page_title = 'Dashboard';
        $customerAmount = $trainerAmount = 0;
        $this->setGlobalSearchValue($request);

        $subAdmins = User::getSubAdminList();
        $notifications = $this->getNotifictaion();
        $ticketCount = $this->getTicketCount();
        $userCount = $this->getUserCount();
        $trainerCount = $this->getTrainer();
        $customerQuetos = $this->getCustomerAmount();
        if ($customerQuetos->count() > 0) {
            foreach ($customerQuetos as $key => $value) {
                if ($value->customerQuote) {
                    $customerAmount += $value->customerQuote->installments->sum('amount');
                }
            }
        }
        $trainerQuetos = $this->getTrainerAmount();
        if ($trainerQuetos->count() > 0) {
            foreach ($trainerQuetos as $key => $value) {
                if ($value->trainerQuote->count() > 0) {
                    foreach ($value->trainerQuote as $invoicesKey => $invoices) {
                        $trainerAmount += $invoices->invoices->sum('amount');
                    }
                }
            }
        }

        return view('dashboards.index')->with([
            'page_title' => $page_title ?? '',
            'subAdmins' => $subAdmins ?? '',
            'notifications' => $notifications ?? '',
            'ticketCount' => $ticketCount ?? '',
            'userCount' => $userCount ?? '',
            'trainerCount' => $trainerCount ?? '',
            'adminSearch' => $this->_adminSearch ?? '',
            'dateStringSearch' => $this->_oldDateString ?? '',
            'dateSearch' => $this->_dateSearch ?? '',
            'customerAmount' => $customerAmount ?? 0,
            'customerInvoiceDate' => $customerInvoiceDate ?? '',
            'trainerAmount' => $trainerAmount ?? 0,
            'trainerInvoiceDate' => $trainerInvoiceDate ?? '',
            'startDate' => ($this->_oldDateString) ? null : $this->_startDate,
            'endDate' => $this->_endDate ?? '',
        ]);
    }

    /**
     * Get the ticket counts
     * 
     * @param $query array search value
     * @return count int
     */
    public function getTicketCount()
    {
        $ticketCount = Ticket::select("*");
        if ($this->_adminSearch) {
            $ticketCount->where('tickets.user_id', '=', $this->_adminSearch);
        }
        if ($this->_startDate) {
            $ticketCount->whereDate('tickets.created_at', '>=', $this->_startDate);
        }
        if ($this->_endDate) {
            $ticketCount->whereDate('tickets.created_at', '<=', $this->_endDate);
        }

        return $ticketCount->count();
    }

    /**
     * Get the user counts
     * 
     * @param $query array search value
     * @return count int
     */
    public function getUserCount()
    {
        $userCount = User::select("*");
        if ($this->_startDate) {
            $userCount->whereDate('created_at', '>=', $this->_startDate);
        }
        if ($this->_endDate) {
            $userCount->whereDate('created_at', '<=', $this->_endDate);
        }

        return $userCount->count();
    }

    /**
     * Get the trainer counts
     * 
     * @param $query array search value
     * @return count int
     */
    public function getTrainer()
    {
        $trainerCount = Trainer::select("*");
        if ($this->_startDate) {
            $trainerCount->whereDate('created_at', '>=', $this->_startDate);
        }
        if ($this->_endDate) {
            $trainerCount->whereDate('created_at', '<=', $this->_endDate);
        }

        return $trainerCount->count();
    }

    /**
     * Get the all notification
     * Default show the current user 10 notifications
     */
    public function getNotifictaion()
    {
        $notification = Notification::getNotifications();

        if ($this->_adminSearch) {
            $notification->where('receiver_id', '=', $this->_adminSearch);
        } else {
            $notification->where('receiver_id', Auth::user()->id);
        }
        if ($this->_startDate) {
            $notification->whereDate('created_at', '>=', $this->_startDate);
        }
        if ($this->_endDate) {
            $notification->whereDate('created_at', '<=', $this->_endDate);
        }

        return $notification->take(5)->get();
    }

    /**
     * Get the customer amount and date
     * 
     * @param $query array search value
     * @return count int
     */
    public function getCustomerAmount()
    {
        $customerAmount = Ticket::select('id', 'user_id')
            ->with([
                'customerQuote:id,customer_id,ticket_id,quote,currency',
                'customerQuote.installments' => function ($q) {
                    $q->where('payment_status', config('constants.PAYMENT.PAID'));
                    if ($this->_startDate) {
                        $q->whereDate('invoice_date', '>=', $this->_startDate);
                    }
                    if ($this->_endDate) {
                        $q->whereDate('invoice_date', '<=', $this->_endDate);
                    }
                },
            ]);
        if ($this->_adminSearch) {
            $customerAmount->where('tickets.user_id', '=', $this->_adminSearch);
        }

        return $customerAmount->get();
    }

    /**
     * Get the customer amount and date
     * 
     * @param $query array search value
     * @return count int
     */
    public function getTrainerAmount()
    {
        $trainerAmount = Ticket::select('id', 'user_id')
            ->with([
                'trainerQuote:id,ticket_id',
                'trainerQuote.invoices' => function ($q) {
                    $q->where('payment_status', config('constants.PAYMENT.PAID'));
                    if ($this->_startDate) {
                        $q->whereDate('invoice_date', '>=', $this->_startDate);
                    }
                    if ($this->_endDate) {
                        $q->whereDate('invoice_date', '<=', $this->_endDate);
                    }
                },
            ]);

        if ($this->_adminSearch) {
            $trainerAmount->where('tickets.user_id', '=', $this->_adminSearch);
        }

        return $trainerAmount->get();
    }

    /**
     * Set the date for search
     * 
     * @param $value date|string
     * @return date
     */
    public function searchStringDate($value)
    {
        $date = '';
        $now = Carbon::now();
        switch ($value) {
            case 'current_month':
                $date = $now->firstOfMonth()->toDateString();
                break;
            case 'last_three_month':
                $date = $now->subMonth(3)->toDateString();
                break;
            case 'current_year':
                $date = $now->startOfYear()->toDateString();
                break;
            default:
                $date = Carbon::parse($value)->toDateString();
                break;
        }

        return $date;
    }

    /**
     * Get all tickets records with pagination, sorting, searching
     * 
     * @param \Illuminate\Http\Reuest $request
     * @return json
     */
    public function getTicketList(Request $request)
    {
        $this->setGlobalSearchValue($request);
        // Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc

        $records = Ticket::select(
            'tickets.id',
            'tickets.ticket_type_id',
            'tickets.user_id',
            'tickets.ticket_id',
            'tickets.created_at',
            'tickets.status',
            'ticket_types.name as ticket_type_name',
        )
            ->leftJoin('ticket_types', 'tickets.ticket_type_id', '=', 'ticket_types.id')
            ->leftJoin('users', 'tickets.user_id', '=', 'users.id')
            ->orderBy($columnName, $columnSortOrder);

        if ($request->ticketStatus != '') {
            $records->where('tickets.status', intval($request->ticketStatus));
        }
        if ($this->_adminSearch) {
            $records->where('tickets.user_id', '=', $this->_adminSearch);
        }
        if ($this->_startDate) {
            $records->whereDate('tickets.created_at', '>=', $this->_startDate);
        }
        if ($this->_endDate) {
            $records->whereDate('tickets.created_at', '<=', $this->_endDate);
        }

        $currentPage = $start / $rowperpage + 1;
        $records = $records->paginate($rowperpage, ["*"], "page", $currentPage)->toArray();
        $response = [
            "draw" => intval($draw),
            "iTotalRecords" => $records['per_page'],
            "iTotalDisplayRecords" => $records['total'],
            "aaData" => $records['data']
        ];

        return response()->json($response, 200);
    }

    /**
     * Get all course records with pagination, sorting, searching
     * 
     * @param \Illuminate\Http\Reuest $request
     * @return json
     */
    public function getCourseList(Request $request)
    {
        $this->setGlobalSearchValue($request);
        // Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = ($columnName_arr[$columnIndex]['data'] === 'formated_created_at') ? 'created_at' : $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc       

        $records = Course::select('id', 'name', 'course_price', 'course_special_price', 'status', 'created_at')
            ->orderBy($columnName, $columnSortOrder);

        if ($request->courseStatus != '') {
            $records->where('courses.status', intval($request->courseStatus));
        }
        if ($this->_startDate) {
            $records->whereDate('courses.created_at', '>=', $this->_startDate);
        }
        if ($this->_endDate) {
            $records->whereDate('courses.created_at', '<=', $this->_endDate);
        }

        $currentPage = $start / $rowperpage + 1;
        $records = $records->paginate($rowperpage, ["*"], "page", $currentPage)->toArray();
        $response = [
            "draw" => intval($draw),
            "iTotalRecords" => $records['per_page'],
            "iTotalDisplayRecords" => $records['total'],
            "aaData" => $records['data']
        ];

        return response()->json($response, 200);
    }

    /**
     * Get all invoice records with pagination, sorting, searching
     * 
     * @param \Illuminate\Http\Reuest $request
     * @return json
     */
    public function getInvoiceList(Request $request)
    {
        $this->setGlobalSearchValue($request);
        // Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = ($columnName_arr[$columnIndex]['data'] === 'formated_created_at') ? 'created_at' : $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc       

        $records = TrainerQuote::select(
            "trainer_quotes.id AS trainer_quotes_id",
            "trainer_quotes.ticket_id",
            "trainer_quotes.trainer_id",
            "tickets.user_id",
            "trainer_quote_invoices.name",
            "trainer_quote_invoices.amount",
            "trainer_quote_invoices.currency",
            "trainer_quote_invoices.invoice_number",
            "trainer_quote_invoices.invoice_date",
            "trainer_quote_invoices.payment_status",
            "trainer_quote_invoices.created_at",
            DB::raw('CONCAT(trainers.first_name, " ", trainers.last_name) AS trainer_full_name')
        )
            ->leftJoin("tickets", "tickets.id", "=", "trainer_quotes.ticket_id")
            ->leftJoin("trainer_quote_invoices", "trainer_quote_invoices.trainer_quote_id", "=", "trainer_quotes.id")
            ->leftJoin("trainers", "trainers.id", "=", "trainer_quotes.trainer_id")
            ->where('trainer_quote_invoices.payment_status', '<>', null)
            ->orderBy($columnName, $columnSortOrder);

        if ($request->invoiceStatus != '') {
            $records->where('trainer_quote_invoices.payment_status', '=', intval($request->invoiceStatus));
        } else {
            $records->where('trainer_quote_invoices.payment_status', '<>', null);
        }

        if ($this->_adminSearch) {
            $records->where('tickets.user_id', '=', $this->_adminSearch);
        }
        if ($this->_startDate) {
            $records->whereDate('trainer_quote_invoices.invoice_date', '>=', $this->_startDate);
        }
        if ($this->_endDate) {
            $records->whereDate('trainer_quote_invoices.invoice_date', '<=', $this->_endDate);
        }

        $currentPage = $start / $rowperpage + 1;
        $records = $records->paginate($rowperpage, ["*"], "page", $currentPage)->toArray();

        $response = [
            "draw" => intval($draw),
            "iTotalRecords" => $records['per_page'],
            "iTotalDisplayRecords" => $records['total'],
            "aaData" => $records['data']
        ];

        return response()->json($response, 200);
    }
}
