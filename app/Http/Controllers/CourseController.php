<?php

namespace TechStudio\Lms\app\Http\Controllers;

use App\Http\Controllers\Controller;
use stdClass;
use App\Models\Course;
use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Requests\Lms\CourseCreateUpdateRequest;
use App\Http\Resources\Lms\CourseResource;
use App\Http\Resources\Lms\CoursesResource;
use App\Http\Resources\Lms\CategoryResource;
use App\Http\Resources\Lms\CourseRoomResource;
use App\Http\Resources\Lms\InstructorResource;
use App\Http\Resources\Lms\CoursePreviewResource;
use App\Http\Resources\Lms\FiltersCourseResource;
use App\Models\Skill;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{

    // private CourseRepositoryInterface $repository;
    // private CategoryRepositoryInterface $categoryRepository;


    // public function __construct(
    //     CourseRepositoryInterface $repository,
    //     CategoryRepositoryInterface $categoryRepository
    // ) {
    //     $this->repository = $repository;
    //     $this->categoryRepository = $categoryRepository;
    // }

    // function getCourseData($courseSlug)
    // {
    //     $course = $this->repository->getBySlug($courseSlug);
    //     $this->repository->incrementField($course->id,'view_count');
    //     if(Auth::check()){
    //         $this->repository->storeView($course->id,Auth::user()->id);
    //     }
    //     return response()->json(new CourseResource($course));
    // }

    // function getCourseRoom($courseSlug)
    // {
    //     // $course = $this->repository->getBySlug($courseSlug);
    //     $course = Course::where('slug', $courseSlug)
    //     ->with( 'instructor', 'rooms.previewMembers','rooms.members','rooms.category')->firstOrFail();
    //     return response()->json(new CourseRoomResource($course));
    // }

    public function getAllCourseData(Request $request)
    {
        $courses = $this->repository->all($request);
        return response()->json(new CoursesResource($courses));
    }

    // public function filters()
    // {
    //     $data = new stdClass();
    //     $data->categories = $this->categoryRepository->getCategories();
    //     $data->skills = $this->repository->getAllSkills();
    //     $data->instructors = $this->repository->getAllInstructors();

    //     return response()->json(new FiltersCourseResource($data));
    // }

    // public function courseList(Request $request)
    // {
    //     $query =Course::with('students');

    //     if ($request->filled('search')) {
    //         $txt = $request->get('search');

    //         $query->where(function ($q) use ($txt) {
    //             $q->where('title', 'like', '%' . $txt . '%');
    //         });
    //     }

    //     $courses = $query->paginate(10);

    //     $data = [
    //         'total' => $courses->total(),
    //         'current_page' => $courses->currentPage(),
    //         'per_page' => $courses->perPage(),
    //         'last_page' => $courses->lastPage(),
    
    //         'data' => $courses->map(function ($course) {
    //             return [
    //                 'id' => $course->id,
    //                 'title' => $course->title,
    //                 'slug' => $course->slug,
    //                 'publicationDate' => $course->publication_date,
    //                 'status' => $course->status,
    //                 'instructor' => new InstructorResource($course->instructor),
    //                 'category' => new CategoryResource($course->category),
    //                 'totalStudentsCount' => $course->students->count(),
    //                 'doneStudentsCount' => $course->students()->where('in_roll', 'done')->count(),
    //                 'rate' => $course->students()->where('rate', '!=', null)->avg('rate'),
    //             ];
    //         }),
    //     ];        

    //     return $data;
    // }

    // public function editCreateCourse(CourseCreateUpdateRequest $courseCreateUpdateRequest) 
    // {

    //     $course = $this->repository->createUpdate($courseCreateUpdateRequest);

    //     new CoursePreviewResource($course);

    //     return $course->id;
        
    // }

    // public function getCommonCreateUpdate()
    // {
    //     $skills = Skill::all()->toArray();

    //     $instructors = $this->repository->getAllInstructors();
    //     $categories = $this->categoryRepository->getCategories();



    //     return [
    //         'level' => [
    //             'beginner', 'intermediate', 'advance'
    //         ],
    //         'features' => [
    //             'گواهینامه شرکت در دوره', 'ضمانت بازگشت وجه', 'پشتیبانی مستقیم مدرس', 'فضای تعاملی',
    //         ],
    //         'skills' => $skills,
    //         'instructor' =>InstructorResource::collection($instructors),
    //         'category' =>CategoryResource::collection($categories),

    //     ];
    // }

    // public function getCommonList()
    // {
    //     $counts = [
    //         'all' => Course::whereNot('status', 'deleted')->count(),
    //         'published' => Course::where('status', 'published')->count(),
    //         'draft' => Course::where('status', 'draft')->count(),
    //         'hidden' => Course::where('status', 'hidden')->count(),
    //     ];

    //     return $counts;
    // }

    // public function getCourse($id)
    // {
    //     $course = Course::where('id', $id)->firstOrFail();

    //     return response()->json(new CourseResource($course));
       
    // }

}
