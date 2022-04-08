<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Ticket;
use App\Http\Requests\SubAdminRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubAdminMail;
use CommonHelper;

class SubAdminController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = 'Sub admin listing';

        return view('subAdmins.index', compact('page_title'));
    }

    /**
     * Get all users records with pagination, sorting, searching
     * 
     * @param \Illuminate\Http\Reuest $request
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
            'call_support',
            'status'
        ];

        //Read value
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
        $statusSearch = $columnName_arr[array_search('status', $tableColumn)]['search']['value'];
        $callSupportSearch = $columnName_arr[array_search('call_support', $tableColumn)]['search']['value'];


        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Fetch records
        $records = User::where([['id', '<>', \Auth::id()], ['role_id', '<>', config('constants.ADMIN_ROLE.SUPER_ADMIN')]])->orderBy($columnName, $columnSortOrder);

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
        if ($statusSearch != '') {
            $records->where('status', intval($statusSearch));
        }
        if ($callSupportSearch != '') {
            $records->where('call_support', intval($callSupportSearch));
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Sub admin create';

        return view('subAdmins.create', compact('page_title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubAdminRequest $request)
    {
        try {
            $plainPassword = $request->password ?? '';
            if (!empty($request->call_support)) {
                $this->removeCallSupport();
            }

            $user = User::create($request->all());
            if ($user) {
                $user->plainPassword = $plainPassword;
                $replaceArr = [
                    'FULL_NAME' => $user->full_name,
                    'PASSWORD' => $user->plainPassword,
                    'EMAIL' => $user->email,
                    'APP_URL' => env('APP_URL')
                ];
                CommonHelper::sendMail($user->email, 'new-user', $replaceArr);
                // Mail::to($user->email)->send(new SubAdminMail($user));
                $request->session()->flash('success', 'Sub admin added successfully.');
            } else {
                $request->session()->flash('error', 'Sub admin not added successfully.');
            }

            return redirect()->route('subadmin.index');
        } catch (\Exception $ex) {
            $request->session()->flash('error', 'Sub admin not added successfully.');

            return redirect()->route('subadmin.create');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id int
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $page_title = 'Sub admin edit';
        $user = User::findorfail($id);
        if ($user->role_id == config('constants.ADMIN_ROLE.SUPER_ADMIN')) {
            $request->session()->flash('error', 'UnAuthorized.');
            return redirect()->route('subadmin.index');
        }

        return view('subAdmins.edit', compact('page_title', 'user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(SubAdminRequest $request, $id)
    {
        try {
            $user = User::findorfail($id);
            if (!empty($request->call_support)) {
                $this->removeCallSupport();
            } else {
                $request->merge(['call_support' => 0]);
            }
            if ($user->update($request->all())) {
                $request->session()->flash('success', 'Sub admin updated successfully.');
            } else {
                $request->session()->flash('error', 'Sub admin not updated successfully.');
            }

            return redirect()->route('subadmin.index');
        } catch (\Exception $ex) {
            $request->session()->flash('error', 'Sub admin not updated successfully.');

            return redirect()->route('subadmin.update');
        }
    }

    /**
     * Delete the specified resource in storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $id)
    {
        try {
            if ($request->ajax()) {
                if(!empty($request->check_allowance)) {
                    $condition = ['user_id', $id];
                    $ticket = Ticket::getUserTicket($condition);
                    
                    return $ticket->count();
                }

                $user = User::findorfail($id);
                if ($user->role_id == config('constants.ADMIN_ROLE.SUPER_ADMIN')) {
                    $request->session()->flash('error', 'UnAuthorized.');
                    return redirect()->route('subadmin.index');
                }
                if ($user->delete()) {
                    $response = ['status' => 1, 'message' => 'Sub admin deleted successfully.'];
                } else {
                    $response = ['status' => 0, 'message' => 'Sub admin not deleted, Please try again.'];
                }

                return response()->json($response);
            }
        } catch (\Exception $ex) {
            $response = ['status' => 0, 'message' => 'Something went wrong, Please try again!'];

            return response()->json($response, 500);
        }
    }

    /**
     * View the specified resource in storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
        $page_title = 'View sub admin details';
        $user = User::findorfail($id);
        if ($user->role_id == config('constants.ADMIN_ROLE.SUPER_ADMIN')) {
            return redirect()->route('subadmin.index')->with('error', 'UnAuthorized');
        }

        return view('subAdmins.view', compact('page_title', 'user'));
    }

    /**
     * Update the status value using ajax
     * 
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function updateStatus(Request $request)
    {
        if ($request->ajax()) {
            $user = User::findorfail($request->id);
            $user->status = ($request->status == 'true') ? config('constants.ACTIVE') : config('constants.INACTIVE');
            if ($user->save()) {
                return true;
            }
            return false;
        }

        return false;
    }

    /**
     * Check the call support is already assign to sub admin
     * 
     * @param \Illuminate\Http\Request
     * @return JsonResponse
     */
    public function checkCallSupport(Request $request)
    {
        $search[] = ['call_support', '=', 1];
        if (!empty($request->id)) {
            $search[] = ['id', '<>', $request->id];
        }
        $user = User::where($search)->first();

        return response()->json([
            'full_name' => !empty($user) ? $user->full_name : ''
        ]);
    }

    /**
     * remove the call support is already assign to sub admin
     * 
     * @return boolean
     */
    public function removeCallSupport()
    {
        $user = User::where('call_support', 1)->update(['call_support' => 0]);

        return ($user) ? true : false;
    }
}
