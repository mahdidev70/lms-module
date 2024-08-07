<?php

namespace TechStudio\Lms\app\Http\Controllers;

use stdClass;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductsResource;
use App\Models\Product;
use App\Models\ProductRegister;
use Illuminate\Support\Facades\Auth;
use TechStudio\Lms\app\Models\Skill;
use Illuminate\Support\Facades\Cache;
use TechStudio\Lms\app\Models\Course;
use TechStudio\Lms\app\Models\Student;
use Illuminate\Support\Facades\Artisan;
use TechStudio\Core\app\Models\Category;
use Illuminate\Support\Facades\Validator;
use TechStudio\Core\app\Models\UserProfile;
use TechStudio\Lms\app\Http\Resources\CourseResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use TechStudio\Lms\app\Http\Resources\CoursesResource;
use TechStudio\Lms\app\Http\Resources\FeatureResource;
use TechStudio\Lms\app\Http\Resources\CategoryResource;
use TechStudio\Lms\app\Http\Resources\CourseRoomResource;
use TechStudio\Lms\app\Http\Resources\InstructorResource;
use TechStudio\Lms\app\Http\Resources\CoursePreviewResource;
use TechStudio\Lms\app\Http\Resources\FiltersCourseResource;
use TechStudio\Lms\app\Http\Requests\CourseCreateUpdateRequest;
use TechStudio\Lms\app\Repositories\Interfaces\CourseRepositoryInterface;
use TechStudio\Lms\app\Repositories\Interfaces\CategoryLmsRepositoryInterface;

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
        if(Auth('sanctum')->check()){
            $this->repository->storeView($course->id,Auth('sanctum')->id());
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
        $minutes = config('cache.short_time')??30;
        $courses = Cache::remember('all_courses', $minutes, function () use ($request) {
            return $this->repository->all($request);
        });
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
            $query->where('instructor_id', $request->input('instructorId'));
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

        $courses = $query->orderBy('id', $sortOrder)->paginate(10);

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
                    'instructor' => $course->instructor ? new InstructorResource($course->instructor):null,
                    'category' => $course->category ? new CategoryResource($course->category):null,
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
       // new CoursePreviewResource($course);
       Artisan::call('course-duration:update', ['courseId' => $course->id]);

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
        $features = $this->repository->getAllFeatures();

        return [
            'level' => [
                'beginner', 'intermediate', 'advance'
            ],
            'features' => FeatureResource::collection($features),
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
        ];

        $categories = Category::where('table_type', get_class($courseModel))->get()->map(function ($category) {
            return [
                'title' => $category->title,
                'slug' => $category->slug,
            ];
        });

        $status = ['published', 'draft', 'hidden'];

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
        $course->shortData = true;
        return response()->json(new CourseResource($course));
    }

    public function editStatus(Request $request)
    {
        $ids = $request['ids'];
        if ($request['status'] == 'published') {

            $date = Carbon::now()->toDateTimeString();
            Course::whereIn('id', $ids)
                    ->update([
                            'status'           => $request['status'],
                            'publication_date' => $date,
                    ]);
            /*$courses = Course::whereIn('id', $ids)->get();

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
            }*/

        } elseif ($request['status'] == 'deleted') {
            Course::query()->whereIn('id', $ids)->delete();
        } else {
            Course::whereIn('id', $ids)
                ->update(['status'=>$request['status']]);
          //  $course->whereIn('id', $ids)->update(['status' => $request['status']]);
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

    public function getUserCourse(Request $request)
    {
        $courseModel = new Course();
        $user = Auth('sanctum')->user();
        $studnetId = Student::where('user_id', $user->id)->get();

        if (class_exists(ProductRegister::class)) {

            $student = ProductRegister::where('user_id', $user->id);

            if ($request['data'] == 'waitingForApprovale') {
                
                $productId = $student->where('register_status', 'pre-register')->where('payment_status', 'waiting_for_approval')->pluck('product_id');
                $products = Product::whereIn('id', $productId)->orderBy('id', 'DESC')->paginate(10);
                return new ProductsResource($products);

            }elseif ($request['data'] == 'payingInstallment') {

                $productId = $student->where('payment_status', 'paying_installment')->pluck('product_id');
                $products = Product::whereIn('id', $productId)->orderBy('id', 'DESC')->paginate(10);
                return new ProductsResource($products);

            }elseif ($request['data'] == 'done') {
                
                $productId = $student->where('payment_status', 'done')->pluck('product_id');
                $products = Product::whereIn('id', $productId)->orderBy('id', 'DESC')->paginate(10);
                return new ProductsResource($products);

            }
        }

        if ($request['data'] == 'necessary') {

            $necessaryCourse = $courseModel->where('necessary', 1)->paginate(10);
            return new CoursesResource($necessaryCourse);

        }elseif ($request['data'] == 'done') {

            $courseDoneId = $studnetId->where('in_roll', 'done')->pluck('course_id');
            $courseDone = Course::whereIn('id', $courseDoneId)->paginate(10);
            return new CoursesResource($courseDone);

        }elseif ($request['data'] == 'bookmark') {

            $courseBookmarkId = $studnetId->where('bookmark', 1)->pluck('course_id');
            $courseBookmark = Course::whereIn('id', $courseBookmarkId)->paginate(10);
            return new CoursesResource($courseBookmark);

        }elseif ($request['data'] == 'inProgress') {

            $courseProgressId = $studnetId->where('in_roll', 'progress')->pluck('course_id');
            $courseProgress = Course::whereIn('id', $courseProgressId)->paginate(10);
            return new CoursesResource($courseProgress);
        }
    }

    public function coursePreview($locale, $id)
    {
        $course = $this->repository->coursePreview($id);
        return response()->json(new CourseResource($course));
    }

}
