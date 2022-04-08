<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailTemplateRequest;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Str;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = __('Email Templates');

        return view('emailTemplates.index', compact('page_title'));
    }

    /**
     * Get all email templates with pagination, sorting, searching
     * 
     * @param \Illuminate\Http\Request $request
     * @return json
     */
    public function getList(Request $request)
    {
        $tableColumn = [];
        $tableColumn = [
            'id',
            'name',
            'subject',
            'slug'
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
        $nameSearch = $columnName_arr[array_search('name', $tableColumn)]['search']['value'];
        $subjectSearch = $columnName_arr[array_search('subject', $tableColumn)]['search']['value'];
        $slugSearch = $columnName_arr[array_search('slug', $tableColumn)]['search']['value'];

        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Fetch records
        $records = EmailTemplate::orderBy($columnName, $columnSortOrder);

        // Search the value
        if (!empty($nameSearch)) {
            $records->where('name', 'like', '%' . $nameSearch . '%');
        }
        if (!empty($subjectSearch)) {
            $records->where('subject', 'like', '%' . $subjectSearch . '%');
        }
        if (!empty($slugSearch)) {
            $records->where('slug', 'like', '%' . $slugSearch . '%');
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
     * Add email template form
     * 
     * @return \Illuminate\View\View
     */
    public function create() {
        $page_title = __('Create Email Template');

        return view('emailTemplates.create', compact('page_title'));
    }

    /**
     * Store email template
     * 
     * @param \App\Http\Requests\EmailTemplateRequest
     * @return \Illuminate\http\Response
     */
    public function store(EmailTemplateRequest $request) {
        try {
            $request->merge(['slug' => Str::slug($request->name)]);
            if(EmailTemplate::create($request->all())) {
                $request->session()->flash('success', __('Email created successfully.'));
            } else {
                $request->session()->flash('error', __('Email template not created, Please try again!.'));
            }

            return redirect()->route('emailtemplate.index');
        } catch(\Exception $ex) {
            $request->session()->flash('error', __('Email template not created, Please try again!.'));

            return redirect()->route('emailtemplate.index');
        }
    }

    /**
     * Update email template form
     * 
     * @param \Illuminate\Http\Request
     * @return \Illuminate\View\View
     */
    public function edit(Request $request) {
        try {
            $page_title = __('Edit Email Template');
            $emailTemplate = EmailTemplate::findOrFail($request->id);

            return view('emailTemplates.update', compact('page_title', 'emailTemplate'));
        } catch(\Exception $ex) {
            $request->session()->flash('error', __('Something went wrong, Please try again!.'));

            return redirect()->route('emailtemplate.index');
        }
    }

    /**
     * Update email template
     * 
     * @param \App\Http\Requests\EmailTemplateRequest
     * @return \Illuminate\http\Response
     */
    public function update(EmailTemplateRequest $request) {
        try {
            $emailTemplate = EmailTemplate::findOrFail($request->id);
            if($emailTemplate->update($request->all())) {
                $request->session()->flash('success', __('Email updated successfully.'));
            } else {
                $request->session()->flash('error', __('Email template not updated, Please try again!.'));
            }

            return redirect()->route('emailtemplate.index');
        } catch(\Exception $ex) {
            $request->session()->flash('error', __('Something went wrong, Please try again!.'));

            return redirect()->route('emailtemplate.index');
        }
    }

    /**
     * View email template
     * 
     * @return \Illuminate\View\View
     */
    public function view(Request $request) {
        try {
            $page_title = __('View Email Template');
            $emailTemplate = EmailTemplate::findOrFail($request->id);

            return view('emailTemplates.view', compact('page_title', 'emailTemplate'));
        } catch(\Exception $ex) {
            $request->session()->flash('error', __('Something went wrong, Please try again!.'));

            return redirect()->route('emailtemplate.index');
        }
    }

    /**
     * Check unique name
     * 
     * @param \Illuminate\Http\Request
     * @return boolean
     */
    public function checkName(Request $request) {
        try {
            $search = [];
            if($request->id) {
                $search[] = ['id', '<>', $request->id];
            }

            $search[] = ['name', '=', trim(preg_replace('/\s\s+/', ' ', $request->name))];
            $data = EmailTemplate::firstWhere($search);

            return ($data) ? 'false' : 'true';
        } catch(\Exception $ex) {
            return 'false';
        }
    }
}
