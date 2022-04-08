<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomer;
use App\Models\Customer;
use App\Models\Ticket;
use CommonHelper;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = __('Customers');

        return view('customers.index', compact('page_title'));
    }

    /**
     * Get all records with pagination, sorting, searching
     * 
     * @param \Illuminate\Http\Request $request
     * @return json
     */
    public function getList(Request $request)
    {
        $tableColumn = [];
        $tableColumn = [
            'id',
            'first_name',
            'last_name',
            'email',
            // 'phone_number',
            'user_type',
            'status'
        ];

        ## Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $firstNameSearch = $columnName_arr[array_search('first_name', $tableColumn)]['search']['value'];
        $lastNameSearch = $columnName_arr[array_search('last_name', $tableColumn)]['search']['value'];
        $emailSearch = $columnName_arr[array_search('email', $tableColumn)]['search']['value'];
        $phoneNumberSearch = $columnName_arr[array_search('phone_number', $tableColumn)]['search']['value'];
        $userTypeSearch = $columnName_arr[array_search('user_type', $tableColumn)]['search']['value'];
        $statusSearch = $columnName_arr[array_search('status', $tableColumn)]['search']['value'];

        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Fetch records
        $records = Customer::orderBy($columnName, $columnSortOrder);

        // Search the value
        if (!empty($firstNameSearch)) {
            $records->where('first_name', 'like', '%' . $firstNameSearch . '%');
        }
        if (!empty($lastNameSearch)) {
            $records->where('last_name', 'like', '%' . $lastNameSearch . '%');
        }
        if (!empty($emailSearch)) {
            $records->where('email', 'like', '%' . $emailSearch . '%');
        }
        if (!empty($phoneNumberSearch)) {
            $records->where('phone_number', 'like', '%' . $phoneNumberSearch . '%');
        }
        if (!empty($userTypeSearch)) {
            $records->where('user_type', intval($userTypeSearch));
        }
        if ($statusSearch != '') {
            $records->where('status', intval($statusSearch));
        }
        $currentPage = $start / $rowperpage + 1;
        $records = $records->paginate($rowperpage, ["*"], "page", $currentPage)->toArray();

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $records['per_page'],
            "iTotalDisplayRecords" => $records['total'],
            "aaData" => $records['data']
        );

        return response()->json($response, 200);
    }

    /**
     * Show form to add the particular resource
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $page_title = __('Create Customer');

        return view('customers.create', compact('page_title'));
    }

    /**
     * Store resource
     * 
     * @param \App\Http\Requests\StoreCustomer
     * @return \Illuminate\http\Response
     */
    public function store(StoreCustomer $request)
    {
        try {
            $request->merge(['is_otp_verified' => 1]);
            $user = Customer::create($request->all());
            if ($user) {
                $replaceArr = [
                    'FULL_NAME' => $user->full_name,
                    'PASSWORD' => $request->password ?? '',
                    'EMAIL' => $user->email,
                    'APP_URL' => env('APP_URL')
                ];
                CommonHelper::sendMail($user->email, 'new-trainer-customer', $replaceArr);
                $request->session()->flash('success', __('Customer created successfully.'));
            } else {
                $request->session()->flash('error', __('Customer not created, Please try again!.'));
            }

            return redirect()->route('customer.index');
        } catch (\Exception $ex) {
            $request->session()->flash('error', __('Something went wrong, Please try again!.'));

            return redirect()->route('customer.index');
        }
    }

    /**
     * Show form to update the particular resource
     * 
     * @param \Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function edit(Request $request)
    {
        try {
            $page_title = __('Edit Customer');
            $user = Customer::findOrFail($request->id);

            return view('customers.update', compact('page_title', 'user'));
        } catch (\Exception $ex) {
            $request->session()->flash('error', __('Something went wrong, Please try again!.'));

            return redirect()->route('customer.index');
        }
    }

    /**
     * Update resource
     * 
     * @param \App\Http\Requests\StoreCustomer
     * @return \Illuminate\http\Response
     */
    public function update(StoreCustomer $request)
    {
        try {
            $user = Customer::findOrFail($request->id);
            !empty($request->avatar_remove) ? $request->merge(['avatar' => null]) : '';
            if ($user->update($request->all())) {
                $request->session()->flash('success', __('Customer updated successfully.'));
            } else {
                $request->session()->flash('error', __('Customer not updated, Please try again!.'));
            }

            return redirect()->route('customer.index');
        } catch (\Exception $ex) {
            $request->session()->flash('error', __('Something went wrong, Please try again!.'));

            return redirect()->route('customer.index');
        }
    }

    /**
     * View resource
     * 
     * @return \Illuminate\View\View
     */
    public function view(Request $request)
    {
        try {
            $page_title = __('View Customer');
            $user = Customer::findOrFail($request->id);

            return view('customers.view', compact('page_title', 'user'));
        } catch (\Exception $ex) {
            $request->session()->flash('error', __('Something went wrong, Please try again!.'));

            return redirect()->route('customer.index');
        }
    }

    /**
     * Delete the specific resource
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function delete(Request $request, $id)
    {
        try {
            if ($request->ajax()) {
                if (!empty($request->check_allowance)) {
                    $condition = ['customer_id', $id];
                    $ticket = Ticket::getUserTicket($condition);

                    return $ticket->count();
                }

                $user = Customer::findOrFail($id);
                if ($user->delete()) {
                    $response = ['status' => 1, 'message' => 'Customer deleted successfully.'];
                } else {
                    $response = ['status' => 0, 'message' => 'Customer not deleted, Please try again.'];
                }

                return response()->json($response);
            }
        } catch (\Exception $ex) {
            $response = ['status' => 0, 'message' => 'Something went wrong, Please try again!'];

            return response()->json($response, 500);
        }
    }

    /**
     * Update the status value using ajax
     * 
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function updateStatus(Request $request)
    {
        $customer = new Customer();
        return CommonHelper::updateStatus($customer, $request);
    }

    /**
     * Check unique email using ajax
     * 
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function checkEmail(Request $request)
    {
        $customer = new Customer();
        return CommonHelper::checkEmail($customer, $request);
    }

    /**
     * Check unique username using ajax
     * 
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function checkUsername(Request $request)
    {
        $customer = new Customer();
        return CommonHelper::checkUsername($customer, $request);
    }
}
