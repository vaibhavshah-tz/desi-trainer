<?php

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use Illuminate\Http\Request;
use App\Models\Trainer;
use App\Models\TrainerComment;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\PrimarySkill;
use App\Http\Requests\TrainerRegistrationRequest;
use Illuminate\Support\Facades\Auth;

class TrainerController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = 'Trainer listing';
        $ticketType = json_encode(array_column(TicketType::getAllTicketType(), 'name', 'id'));

        return view('trainer.index', compact('page_title', 'ticketType'));
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
            'skill_title',
            'ticket_types',
            'primary_skills',
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
        $skillTitleSearch = $columnName_arr[array_search('skill_title', $tableColumn)]['search']['value'];
        $statusSearch = $columnName_arr[array_search('status', $tableColumn)]['search']['value'];
        $ticketTypesSearch = $columnName_arr[array_search('ticket_types', $tableColumn)]['search']['value'];
        $primarySkillSearch = $columnName_arr[array_search('primary_skills', $tableColumn)]['search']['value'];

        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        $records = Trainer::select('trainers.id', 'trainers.first_name', 'trainers.last_name', 'trainers.skill_title', 'trainers.phone_number', 'trainers.status', 'course_categories.name as course_categories_name')
            ->with(['ticketTypes', 'primarySkills'])
            ->join('course_categories', 'trainers.course_category_id', '=', 'course_categories.id')
            ->orderBy($columnName, $columnSortOrder);

        // Search the value
        if (!empty($ticketTypesSearch)) {
            $records->whereHas('ticketTypes', function ($query) use ($ticketTypesSearch) {
                $query->whereIn('ticket_types.id', [$ticketTypesSearch]);
            });
        }
        if (!empty($skillTitleSearch)) {
            $records->where('trainers.skill_title', 'like', '%' . $skillTitleSearch . '%');
        }

        if (!empty($courseCategoryName)) {
            $records->where('course_categories.name', 'like', '%' . $courseCategoryName . '%');
        }

        if (!empty($firstNameSearch)) {
            $records->orWhereRaw(
                "concat(first_name, ' ', last_name) like '%" . $firstNameSearch . "%' "
            );
        }
        // Search the value skills
        if (!empty($primarySkillSearch)) {
            $records->whereHas('primarySkills', function ($query) use ($primarySkillSearch) {
                $query->where('primary_skills.name', 'like', '%' . $primarySkillSearch . '%');
            });
        }

        if ($statusSearch != '') {
            $records->where('trainers.status', intval($statusSearch));
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
        $page_title = 'Trainer create';


        return view('trainer.create', compact('page_title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TrainerRegistrationRequest $request)
    {
        try {
            $trainerComment = [];
            $data = $request->all();
            $data['is_otp_verified'] = 1;
            unset($data['user_id']);
            if (!empty($data['note'])) {
                $trainerComment['user_id'] = Auth::user()->id;
                $trainerComment['note'] = $data['note'];
                unset($data['note']);
            }

            $trainer = Trainer::create($data);
            if ($trainer) {
                $trainer->saveTicketType($trainer, $request->ticket_type);
                $trainer->savePrimarySkill($trainer, $request->primary_skill);
                if (!empty($trainerComment)) {
                    $trainer->trainerComments()->create($trainerComment);
                }
                $replaceArr = [
                    'FULL_NAME' => $trainer->full_name,
                    'PASSWORD' => $data['password'],
                    'EMAIL' => $trainer->email,
                ];
                CommonHelper::sendMail($trainer->email, 'new-trainer-customer', $replaceArr);
                $request->session()->flash('success', 'Trainer added successfully.');
            } else {
                $request->session()->flash('error', 'Trainer not added successfully.');
            }

            return redirect()->route('trainer.index');
        } catch (\Exception $ex) {
            $request->session()->flash('error', 'Trainer not added successfully.');

            return redirect()->route('trainer.create');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id int
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = 'Trainer edit';
        $trainer = Trainer::with(['primarySkills', 'ticketTypes', 'trainerComments.user'])->findorfail($id);
        $primarySkill = PrimarySkill::getSkillByCourseCategory($trainer->course_category_id)->toArray();

        return view('trainer.edit', compact('page_title', 'trainer', 'primarySkill'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(TrainerRegistrationRequest $request, $id)
    {
        try {
            $trainer = Trainer::findorfail($id);
            $data = $request->all();
            unset($data['user_id']);
            if (!empty($data['note'])) {
                $trainerComment['note'] = $data['note'];
                unset($data['note']);
            }
            if ($trainer->update($data)) {
                $trainer->saveTicketType($trainer, $request->ticket_type);
                $trainer->savePrimarySkill($trainer, $request->primary_skill);
                if (!empty($trainerComment['note'])) {
                    TrainerComment::updateOrCreate([
                        'trainer_id'   => $trainer->id,
                        'user_id'   => Auth::user()->id
                    ], [
                        'note'     => $trainerComment['note']
                    ]);
                }

                $request->session()->flash('success', 'Trainer updated successfully.');
            } else {
                $request->session()->flash('error', 'Trainer not updated successfully.');
            }

            return redirect()->route('trainer.index');
        } catch (\Exception $ex) {
            $request->session()->flash('error', 'Trainer not updated successfully.');

            return redirect()->route('trainer.index');
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
                if (!empty($request->check_allowance)) {
                    $condition = ['trainer_id', $id];
                    $ticket = Ticket::getUserTicket($condition);

                    return $ticket->count();
                }

                $trainer = Trainer::findorfail($id);
                if ($trainer->delete()) {
                    $response = ['status' => 1, 'message' => 'Trainer deleted successfully.'];
                } else {
                    $response = ['status' => 0, 'message' => 'Trainer not deleted, Please try again.'];
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
        $page_title = 'View trainer details';
        $trainer = Trainer::with(['primarySkills', 'ticketTypes', 'trainerComments.user'])->findorfail($id);

        return view('trainer.view', compact('page_title', 'trainer'));
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
            $trainer = Trainer::findorfail($request->id);
            $trainer->status = ($request->status == 'true') ? config('constants.ACTIVE') : config('constants.INACTIVE');
            if ($trainer->save()) {
                return true;
            }
            return false;
        }

        return false;
    }

    /**
     * Check unique email
     * 
     * @param \Illuminate\Http\Request
     * @return boolean
     */
    public function checkEmail(Request $request)
    {
        $trainer = new Trainer();
        return CommonHelper::checkEmail($trainer,  $request);
    }

    /**
     * Check unique username
     * 
     * @param \Illuminate\Http\Request
     * @return boolean
     */
    public function checkUsername(Request $request)
    {
        $trainer = new Trainer();
        return CommonHelper::checkUsername($trainer,  $request);
    }
}
