<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CourseController extends ApiController
{
    /**
     * Trending courses
     * URL - /v1/trending-courses
     * 
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function trendingCourses(Request $request)
    {
        try {
            $courses = $this->getCourses($request, 1);

            return $this->apiResponse->respondWithMessageAndPayload($courses, __("Record fetched successfully"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * All courses
     * URL - /v1/all-courses
     * 
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function allCourses(Request $request)
    {
        try {
            $courses = $this->getCourses($request);

            return $this->apiResponse->respondWithMessageAndPayload($courses, __("Record fetched successfully"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Get paginated courses
     * 
     * @param \Illuminate\Http\Request $request
     * @param boolean $isTrending
     * @return object|JsonResponse
     */
    public function getCourses(Request $request, $isTrending = 0)
    {
        try {
            $perPage = $request->get('limit', 10);
            $categoryId = $request->get('category_id') ?? '';
            $search = $request->get('search') ?? '';
            $courses = Course::active()->with(['courseCategory:id,name'])->orderBy('created_at', 'desc');

            if (!empty($isTrending)) {
                $courses->where('trending', '=', 1);
            } else {
                $courses->where('trending', '=', 0);
            }

            if (!empty($categoryId)) {
                $courses->whereHas('courseCategory', function ($q) use ($categoryId) {
                    $q->where('id', '=', $categoryId);
                });
            }

            if (!empty($search)) {
                $courses->where('name', 'LIKE', '%' . $search . '%');
            }

            return $courses->paginate($perPage);
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }

    /**
     * Get course details
     * 
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function getCourse(Request $request)
    {
        try {
            $course = Course::with([
                'courseCategory:id,name',
                'courseCurriculumns:id,course_id,parent_id,title,description',
                'courseCurriculumns.children:id,course_id,parent_id,title,description',
                'courseFaqs:id,course_id,title,description',
                'courseFeatures:id,course_id,title'
            ])->findorfail($request->id);

            return $this->apiResponse->respondWithMessageAndPayload($course, __("Record fetched successfully"));
        } catch (\Exception $ex) {
            return $this->apiResponse->handleAndResponseException($ex);
        }
    }
}
