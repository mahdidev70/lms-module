<?php

namespace TechStudio\Lms\app\Http\Controllers;

use App\Http\Controllers\Controller;
use TechStudio\Lms\app\Models\Course;
use TechStudio\Lms\app\Models\Skill;
use stdClass;
use Illuminate\Http\Request;
use Carbon\Carbon;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;
use TechStudio\Core\app\Models\Category;
use TechStudio\Core\app\Models\UserProfile;
use TechStudio\Lms\app\Http\Requests\CourseCreateUpdateRequest;
use TechStudio\Lms\app\Http\Resources\CategoryResource;
use TechStudio\Lms\app\Http\Resources\CoursePreviewResource;
use TechStudio\Lms\app\Http\Resources\CourseResource;
use TechStudio\Lms\app\Http\Resources\CourseRoomResource;
use TechStudio\Lms\app\Http\Resources\CoursesResource;
use TechStudio\Lms\app\Http\Resources\FiltersCourseResource;
use TechStudio\Lms\app\Http\Resources\InstructorResource;
use TechStudio\Lms\app\Models\Student;
use TechStudio\Lms\app\Repositories\Interfaces\CategoryLmsRepositoryInterface;
use TechStudio\Lms\app\Repositories\Interfaces\CourseRepositoryInterface;

class CourseController extends Controller
{
    private CourseRepositoryInterface $repository;
    private CategoryLmsRepositoryInterface $categoryRepository;

    public function __construct(
        CourseRepositoryInterface $repository,
        CategoryLmsRepositoryInterface $categoryRepository
    ) {
        $this->repository = $repository;
        $this->categoryRepository = $categoryRepository;
    }

    function getCourseData($local, $courseSlug)
    {
        $course = $this->repository->getBySlug($courseSlug);
        $this->repository->incrementField($course->id,'view_count');
        if(Auth::check()){
            $this->repository->storeView($course->id,Auth::user()->id);
        }
        return response()->json(new CourseResource($course));
    }

    function getCourseRoom($courseSlug)
    {
        $course = Course::where('slug', $courseSlug)
        ->with( 'instructor', 'rooms.previewMembers','rooms.members','rooms.category')->firstOrFail();
        return response()->json(new CourseRoomResource($course));
    }

    public function getAllCourseData(Request $request)
    {
        $courses = $this->repository->all($request);
        return response()->json(new CoursesResource($courses));
    }

    public function filters()
    {
        $data = new stdClass();
        $data->categories = $this->categoryRepository->getCategories();
        $data->skills = $this->repository->getAllSkills();
        $data->instructors = $this->repository->getAllInstructors();
        // return $data;
        return response()->json(new FiltersCourseResource($data));
    }

    public function courseList(Request $request)
    {
        $query = Course::with('students');
        if ($request->filled('search')) {
            $txt = $request->get('search');

            $query->where(function ($q) use ($txt) {
                $q->where('title', 'like', '%' . $txt . '%');
            });
        }

        $user = new UserProfile();

        //Filtering
        if (isset($request->instructorId) && $request->instructorId != null ) {
            $query->where('instructor_id', $request->input('instructor_id'));
        }
        if (isset($request->instructorType) && $request->instructorType != null) {
            if ($request->instructorType == 'user') {
                $query->where('instructor_type', get_class($user));
            }
        }
        if (isset($request->categorySlug) && $request->categorySlug != null) {
            $query->whereHas('category', function ($categoryQuery) use ($request) {
                $categoryQuery->where('slug', $request->input('categorySlug'));
            });
        }
        if (isset($request->status) && $request->status != null ) {
            $query->where('status', $request->input('status'));
        }

        //sort data
        $sortOrder= 'desc';
        if (isset($request->sortOrder) && ($request->sortOrder ==  'asc' || $request->sortOrder ==  'desc')) {
            $sortOrder = $request->sortOrder;
        }

        if ($request->has('sortKey')) {

            if ($request->sortKey == 'publicationDate') {
                $query->orderBy('publication_date', $sortOrder);
            } elseif ($request->sortKey == 'studentCount') {
                $query->withCount('students')->orderBy('students_count', $sortOrder);
            } elseif ($request->sortKey == 'graduateStudentCount') {
                $query->withCount(['students' => function ($q) {
                    $q->where('in_roll', 'done');
                }])->orderBy('students_count', $sortOrder)->orderBy('publication_date', $sortOrder);
            } elseif ($request->sortKey == 'rate') {
                $query->withCount(['students' => function ($q) {
                    $q->whereNotNull('rate');
                }])->orderBy('students_count', $sortOrder);
            }
        }


        $courses = $query->paginate(10);

        $data = [
            'total' => $courses->total(),
            'current_page' => $courses->currentPage(),
            'per_page' => $courses->perPage(),
            'last_page' => $courses->lastPage(),

            'data' => $courses->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'slug' => $course->slug,
                    'publicationDate' => $course->publication_date,
                    'status' => $course->status,
                    'instructor' => new InstructorResource($course->instructor),
                    'category' => new CategoryResource($course->category),
                    'totalStudentsCount' => $course->students->count(),
                    'doneStudentsCount' => $course->students()->where('in_roll', 'done')->count(),
                    'rate' => $course->students()->where('rate', '!=', null)->avg('rate'),
                ];
            }),
        ];
        return $data;
    }

    public function editCreateCourse(CourseCreateUpdateRequest $courseCreateUpdateRequest)
    {

        $course = $this->repository->createUpdate($courseCreateUpdateRequest);
        new CoursePreviewResource($course);

        return response()->json([
            'message' => 'تغییرات با موفقیت ثبت شد.',
            'id' => $course->id,
        ], 200);
    }

    public function getCommonCreateUpdate()
    {
        $skills = Skill::all()->toArray();
        $instructors = $this->repository->getAllInstructors();
        $categories = $this->categoryRepository->getCategories();

        return [
            'level' => [
                'beginner', 'intermediate', 'advance'
            ],
            'features' => [
                'گواهینامه شرکت در دوره', 'ضمانت بازگشت وجه', 'پشتیبانی مستقیم مدرس', 'فضای تعاملی',
            ],
            'skills' => $skills,
            'instructor' =>InstructorResource::collection($instructors),
            'category' =>CategoryResource::collection($categories),

        ];
    }

    public function getCommonList()
    {
        $courseModel = new Course();

        $counts = [
            'all' => $courseModel->whereNot('status', 'deleted')->count(),
            'published' => $courseModel->where('status', 'published')->count(),
            'draft' => $courseModel->where('status', 'draft')->count(),
            'hidden' => $courseModel->where('status', 'hidden')->count(),
            'deleted' => $courseModel->where('status', 'deleted')->count(),
        ];

        $categories = Category::where('table_type', get_class($courseModel))->get()->map(function ($category) {
            return [
                'title' => $category->title,
                'slug' => $category->slug,
            ];
        });

        $status = ['published', 'draft', 'hidden', 'deleted'];

        $instructors = $this->repository->getAllInstructors();

        return [
            'counts' => $counts,
            'categories' => $categories,
            'status' => $status,
            'instructors' => InstructorResource::collection($instructors),
        ];
    }

    public function getCourse($local, $id)
    {
        $course = Course::where('id', $id)->firstOrFail();
        return response()->json(new CourseResource($course));
    }

    public function editStatus(Request $request)
    {
        $ids = $request['ids'];

        if ($request['status'] == 'published') {

            $date = Carbon::now()->toDateTimeString();
            $courses = Course::whereIn('id', $ids)->get();

            foreach ($courses as $course) {
                $data = Validator::make($course->toArray(), [
                    'title' => 'required',
                    'slug' => 'required',
                    'instructor_type' => 'required',
                    'instructor_id' => 'required',
                    'category_id' => 'required',
                    'description' => 'required',
                    'FAQ' => 'required',
                    'features' => 'required',
                    'languages' => 'required',
                    'learning_points' => 'required',
                    'support_items' => 'required',
                    'level' => 'required',
                    'banner_url' => 'required',
                ])->validate();

                $course->whereIn('id', $ids)->update([
                    'status' => 'published',
                    'publication_date' => $date,
                ]);
            }

        }else {
            $course->whereIn('id', $ids)->update(['status' => $request['status']]);
        }

        return [
            'updateCourses' => $ids,
        ];
    }

    public function viewDashboard()
    {
        $courses = Course::with('students')->get();
        $students = new Student();

        foreach ($courses as $course) {
            $data[] = [
                'courseCount' => $courses->count(),
                'publishCourseCount' => $courses->where('status', 'published')->count(),
                'graduateStudentCount' => $students->where('in_roll', 'done')->count(),
                'progressStudentCount' => $students->where('in_roll', 'progress')->count(),
                'bestCourse' => [
                    'id' => $course->id,
                    'title' => $course->title,
                    'rateAve' => $course->students()->where('rate', '!=', null)->avg('rate'),
                    'viewCount' => $course->view_count,
                ]
            ];
        };

        return $data;
    }

}
