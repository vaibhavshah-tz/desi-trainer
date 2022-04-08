<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class InterestedTrainerController extends Controller
{
    /**
     * Get trainer listing
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $page_title = 'Interested Trainer Listing';

        return view('interested-trainers.index', compact('page_title'));
    }

    /**
     * Get interested trainers records with pagination, sorting, searching
     * 
     * @param \Illuminate\Http\Reuest $request
     * @return json
     */
    public function getList(Request $request)
    {
        $tableColumn = [
            'id',
            'first_name',
            'email',
            'skill_title',
            // 'category',
            'primary_skills',
            'total_experience_year'
        ];
        //Read value
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page
        $ticketId = $request->route('id');

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');

        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        $userType = $request->get('type');
        $userId = $request->get('user');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index        
        $nameSearch = $columnName_arr[array_search('first_name', $tableColumn)]['search']['value'];
        $emailSearch = $columnName_arr[array_search('email', $tableColumn)]['search']['value'];
        $titleSearch = $columnName_arr[array_search('skill_title', $tableColumn)]['search']['value'];
        $categorySearch = $columnName_arr[array_search('category', $tableColumn)]['search']['value'];
        $primarySkillsSearch = $columnName_arr[array_search('primary_skills', $tableColumn)]['search']['value'];
        $totalExpSearch = $columnName_arr[array_search('total_experience_year', $tableColumn)]['search']['value'];

        $columnName = !empty($tableColumn[$columnIndex]) ? $tableColumn[$columnIndex] : 'interested_ticket_trainer.created_at'; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc

        $searchValue = $search_arr['value']; // Search value

        $record = Ticket::select('tickets.id', 'tickets.trainer_id', 'tickets.ticket_id')
            ->findOrFail($ticketId);

        $records = $record->interestedTrainers()->active()
            ->with([
                'primarySkills:primary_skills.id,primary_skills.name',
                // 'courseCategory:course_categories.id,course_categories.name'
            ])
            ->select(['trainers.id', 'trainers.first_name', 'trainers.last_name', 'trainers.email', 'trainers.course_category_id', 'trainers.skill_title', 'trainers.total_experience_year', 'trainers.total_experience_month'])
            ->orderBy($columnName, $columnSortOrder);

        if (!empty($nameSearch)) {
            $records->whereRaw(
                "concat(first_name, ' ', last_name) like '%" . $nameSearch . "%' "
            );
        }
        if (!empty($emailSearch)) {
            $records->where('email', 'like', '%' . $emailSearch . '%');
        }
        if (!empty($titleSearch)) {
            $records->where('skill_title', 'like', '%' . $titleSearch . '%');
        }
        if (!empty($categorySearch)) {
            $records->whereHas('courseCategory', function ($query) use ($categorySearch) {
                $query->where('name', 'like', '%' . $categorySearch . '%');
            });
        }
        if (!empty($primarySkillsSearch)) {
            $records->whereHas('primarySkills', function ($query) use ($primarySkillsSearch) {
                $query->where('name', 'like', '%' . $primarySkillsSearch . '%');
            });
        }
        if (!empty($totalExpSearch)) {
            $records->whereRaw(
                "concat(total_experience_year, '.', total_experience_month, ' yrs') like '%" . $totalExpSearch . "%' "
            );
        }

        $currentPage = $start / $rowperpage + 1;
        $records = $records->paginate($rowperpage, ["*"], "page", $currentPage);

        return response()->view(
            'interested-trainers.get-list',
            compact(['records', 'rowperpage', 'currentPage', 'draw', 'ticketId'])
        );
    }
}
