<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseCurriculumn;
use App\Models\CourseFaq;
use App\Models\Ticket;
use App\Http\Requests\CourseRequest;
use App\Models\Trainer;
use App\Traits\CourseResponse;

class CoursesController extends Controller
{
    use CourseResponse;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = 'Course listing';

        return view('courses.index', compact('page_title'));
    }

    /**
     * Get all course category records with pagination, sorting, searching
     * 
     * @param \Illuminate\Http\Reuest $request
     * @return json
     */
    public function getList(Request $request)
    {
        $tableColumn = [];
        $tableColumn = [
            'id',
            'name',
            'course_categories_name',
            'course_price',
            'course_special_price',
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

        $nameSearch = $columnName_arr[array_search('name', $tableColumn)]['search']['value'];
        $statusSearch = $columnName_arr[array_search('status', $tableColumn)]['search']['value'];
        $courseCategoryName = $columnName_arr[array_search('course_categories_name', $tableColumn)]['search']['value'];
        $coursePrice = $columnName_arr[array_search('course_price', $tableColumn)]['search']['value'];
        $courseSpecialPrice = $columnName_arr[array_search('course_special_price', $tableColumn)]['search']['value'];

        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value
        $records = Course::select('courses.id', 'courses.name', 'courses.currency', 'courses.course_price', 'courses.course_special_price', 'courses.status', 'course_categories.name as course_categories_name')
            ->join('course_categories', 'courses.course_category_id', '=', 'course_categories.id')
            ->orderBy($columnName, $columnSortOrder);

        if (!empty($courseCategoryName)) {
            $records->where('course_categories.name', 'like', '%' . $courseCategoryName . '%');
        }
        if (!empty($nameSearch)) {
            $records->where('courses.name', 'like', '%' . $nameSearch . '%');
        }
        if ($statusSearch != '') {
            $records->where('courses.status', intval($statusSearch));
        }
        if (!empty($coursePrice)) {
            $records->whereRaw(
                "concat(courses.currency, ' ', courses.course_price) like '%" . $coursePrice . "%' "
            );
        }
        if (!empty($courseSpecialPrice)) {
            $records->whereRaw(
                "concat(courses.currency, ' ', courses.course_special_price) like '%" . $courseSpecialPrice . "%' "
            );
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
        $courseKeyFeature = $course = [];
        $page_title = 'Course create';
        $trainer = Trainer::getTrainer();

        return view('courses.create', compact('page_title', 'courseKeyFeature', 'course', 'trainer'));
    }


    /**
     * Save the course details
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveDetails(CourseRequest $request)
    {
        try {
            if ($request->ajax()) {
                $course = Course::updateOrCreate([
                    'id'   => $request->id,
                ], [
                    'course_category_id'     => $request->get('course_category_id'),
                    'name' => $request->get('name'),
                    'description'    => $request->get("description"),
                    'currency'   => $request->get('currency'),
                    'course_price'       => $request->get('course_price'),
                    'course_special_price'   => $request->get('course_special_price'),
                    'cover_image'    => $request->cover_image,
                    'status'    => $request->get('status'),
                    'trending'    => $request->trending ?? 0,
                    'trainer_id' => $request->trainer_id ?? null
                ]);
                if ($course) {
                    return response()->json($this->sendSuccessResponse($course, "Course added successfully"));
                }
                return response()->json($this->sendErrorResponse("Course not added successfully"));
            }
        } catch (\Exception $ex) {
            return response()->json($this->sendErrorResponse($ex->getMessage(), $ex->getCode()));
        }
    }

    /**
     * Save the multiple key features
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveKeyFeatures(Request $request)
    {
        try {
            if ($request->ajax()) {
                $course = Course::findorfail($request->id);
                if ($course) {
                    $course->saveCourseFeature($course, $request->course_features);
                    return response()->json($this->sendSuccessResponse($course, "Key features added successfully"));
                }
                return response()->json($this->sendErrorResponse("Key features not added successfully"));
            }
        } catch (\Exception $ex) {
            return response()->json($this->sendErrorResponse($ex->getMessage(), $ex->getCode()));
        }
    }

    /**
     * Save the curriculum data
     * Save the section and topic data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveCourseCurriculum(Request $request)
    {
        try {
            if ($request->ajax()) {
                $courseCurriculum = CourseCurriculumn::updateOrCreate([
                    'id'   => !empty($request->parent_id) ? $request->topicId : $request->curriculumId,
                ], [
                    'course_id'     => $request->course_id ?? 1,
                    'parent_id' => $request->parent_id,
                    'title'    => $request->title,
                    'description'   => $request->description,
                ]);

                if ($courseCurriculum) {
                    return response()->json($this->sendSuccessResponse($courseCurriculum, "Curriculum data has been added successfully"));
                }
                return response()->json($this->sendErrorResponse("Curriculum data has been not added successfully"));
            }
        } catch (\Exception $ex) {
            return response()->json($this->sendErrorResponse($ex->getMessage(), $ex->getCode()));
        }
    }


    /**
     * Get the section listing
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response JSON
     */
    public function getSectionListing(Request $request)
    {
        try {
            if ($request->ajax()) {
                $courseCurriculum = CourseCurriculumn::findorfail($request->id);

                if ($courseCurriculum) {
                    return response()->json($this->sendSuccessResponse($courseCurriculum, "Curriculum section has been added successfully"));
                }
                return response()->json($this->sendErrorResponse("Curriculum section has been not added successfully"));
            }
        } catch (\Exception $ex) {
            return response()->json($this->sendErrorResponse($ex->getMessage(), $ex->getCode()));
        }
    }

    /**
     * Get the section only parent title
     * Which is shown in sidebar
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response JSON
     */
    public function getParentSectionListing(Request $request)
    {
        try {
            if ($request->ajax()) {
                $course = CourseCurriculumn::select('id', 'title')->where([
                    ['course_id', $request->id],
                    ['parent_id', 0],
                ])
                    ->orderby('id', 'DESC')
                    ->get();

                if ($course) {
                    return response()->json($this->sendSuccessResponse($course));
                }
                return response()->json($this->sendErrorResponse("Error occured!!"));
            }
        } catch (\Exception $ex) {
            return response()->json($this->sendErrorResponse($ex->getMessage(), $ex->getCode()));
        }
    }

    /**
     * Delete the section data
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response JSON
     */
    public function deleteSection(Request $request)
    {
        try {
            if ($request->ajax()) {
                $course = CourseCurriculumn::where('id', $request->id)->orWhere('parent_id', $request->id)->delete();
                if ($course) {
                    return response()->json($this->sendSuccessResponse($course, 'Section has been deleted successfully'));
                }
                return response()->json($this->sendErrorResponse("Error occured!!"));
            }
        } catch (\Exception $ex) {
            return response()->json($this->sendErrorResponse($ex->getMessage(), $ex->getCode()));
        }
    }

    /**
     * Get the topic listing
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response JSON
     */
    public function getTopicListing(Request $request)
    {
        try {
            if ($request->ajax()) {
                $course = CourseCurriculumn::select('id', 'title', 'description', 'course_id', 'parent_id')->where([
                    ['course_id', $request->id],
                    ['parent_id', $request->parent_id],
                ])
                    ->orderby('id', 'DESC')
                    ->get();

                if ($course) {
                    return response()->json($this->sendSuccessResponse($course));
                }
                return response()->json($this->sendErrorResponse("Error occured!!"));
            }
        } catch (\Exception $ex) {
            return response()->json($this->sendErrorResponse($ex->getMessage(), $ex->getCode()));
        }
    }

    /**
     * Delete the topic data
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response JSON
     */
    public function deleteTopic(Request $request)
    {
        try {
            if ($request->ajax()) {
                $course = CourseCurriculumn::findorfail($request->id);
                if ($course->delete()) {
                    return response()->json($this->sendSuccessResponse($course, 'Topic has been deleted successfully'));
                }
                return response()->json($this->sendErrorResponse("Error occured!!"));
            }
        } catch (\Exception $ex) {
            return response()->json($this->sendErrorResponse($ex->getMessage(), $ex->getCode()));
        }
    }

    /**
     * Save the FAQ information
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response JSON
     */
    public function saveFaq(Request $request)
    {
        try {
            if ($request->ajax()) {
                $courseCurriculum = CourseFaq::updateOrCreate([
                    'id'   => $request->faqId,
                ], [
                    'course_id'     => $request->course_id,
                    'title'    => $request->title,
                    'description'   => $request->description,
                ]);

                if ($courseCurriculum) {
                    return response()->json($this->sendSuccessResponse($courseCurriculum, "FAQ has been saved successfully"));
                }
                return response()->json($this->sendErrorResponse("FAQ has been saved successfully"));
            }
        } catch (\Exception $ex) {
            return response()->json($this->sendErrorResponse($ex->getMessage(), $ex->getCode()));
        }
    }

    /**
     * Get the FAQ listing
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response JSON
     */
    public function getFaqListing(Request $request)
    {
        try {
            if ($request->ajax()) {
                $courseFaq = CourseFaq::select('id', 'title', 'description', 'course_id')->where([
                    ['course_id', $request->course_id]
                ])
                    ->orderby('id', 'DESC')
                    ->get();

                if ($courseFaq) {
                    return response()->json($this->sendSuccessResponse($courseFaq));
                }
                return response()->json($this->sendErrorResponse("Error occured!!"));
            }
        } catch (\Exception $ex) {
            return response()->json($this->sendErrorResponse($ex->getMessage(), $ex->getCode()));
        }
    }

    /**
     * Get the FAQ single records
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response JSON
     */
    public function getFaq(Request $request)
    {
        try {
            if ($request->ajax()) {
                $courseFaq = CourseFaq::findorfail($request->id);

                if ($courseFaq) {
                    return response()->json($this->sendSuccessResponse($courseFaq));
                }
                return response()->json($this->sendErrorResponse("Error occured!!"));
            }
        } catch (\Exception $ex) {
            return response()->json($this->sendErrorResponse($ex->getMessage(), $ex->getCode()));
        }
    }

    /**
     * Delete the FAQ data
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response JSON
     */
    public function deleteFaq(Request $request)
    {
        try {
            if ($request->ajax()) {
                $course = CourseFaq::findorfail($request->id);
                if ($course->delete()) {
                    return response()->json($this->sendSuccessResponse($course, 'FAQ has been deleted successfully'));
                }
                return response()->json($this->sendErrorResponse("Error occured!!"));
            }
        } catch (\Exception $ex) {
            return response()->json($this->sendErrorResponse($ex->getMessage(), $ex->getCode()));
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
        $page_title = 'Course edit';
        $courseDetails = Course::with(['courseCurriculumns', 'courseFaqs', 'courseFeatures', 'trainer'])->findorfail($id);
        $trainer = Trainer::getTrainer();

        return view('courses.edit', compact('page_title', 'courseDetails', 'trainer'));
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
                    $condition = ['course_id', $id];
                    $ticket = Ticket::getUserTicket($condition);

                    return $ticket->count();
                }

                $courses = Course::findorfail($id);
                if ($courses->delete()) {
                    $response = ['status' => 1, 'message' => 'Course deleted successfully.'];
                } else {
                    $response = ['status' => 0, 'message' => 'Course not deleted, Please try again.'];
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
        $page_title = 'View course details';
        $course = Course::with([
            'courseCategory:id,name',
            'courseCurriculumns:id,course_id,parent_id,title,description',
            'courseFaqs:id,course_id,title,description',
            'courseFeatures:id,course_id,title',
            'trainer:id,first_name,last_name'
        ])->findorfail($id);

        return view('courses.view', compact('page_title', 'course'));
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
            $trainer = Course::findorfail($request->id);
            $trainer->status = ($request->status == 'true') ? config('constants.ACTIVE') : config('constants.INACTIVE');
            if ($trainer->save()) {
                return true;
            }
            return false;
        }

        return false;
    }

    /**
     * Get the course category wise course list
     * Set the value in select2 dropdown
     * 
     * @param $request
     * @return array
     */
    public function getCategoryCourse(Request $request)
    {
        $courseListArr = [];
        if ($request->ajax()) {
            $courseListArr = Course::getCourse($request->course_category_id);
        }

        return $courseListArr;
    }
}
