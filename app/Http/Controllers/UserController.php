<?php

namespace TechStudio\Lms\app\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use stdClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use TechStudio\Lms\app\Http\Resources\InstructorsResource;
use TechStudio\Lms\app\Http\Resources\UserHomeResource;
use TechStudio\Lms\app\Repositories\Interfaces\CategoryLmsRepositoryInterface;
use TechStudio\Lms\app\Repositories\Interfaces\CommentRepositoryInterface;
use TechStudio\Lms\app\Repositories\Interfaces\CourseRepositoryInterface;
use TechStudio\Lms\app\Repositories\Interfaces\StudentRepositoryInterface;
use TechStudio\Lms\app\Repositories\Interfaces\UserRepositoryInterface;

class UserController extends Controller
{
    private UserRepositoryInterface $userRepository;
    private CourseRepositoryInterface $courseRepository;
    private StudentRepositoryInterface $studentRepository;
    private CommentRepositoryInterface $commentRepository;
    private CategoryLmsRepositoryInterface $categoryRepository;

    public function __construct(
        CourseRepositoryInterface $courseRepository,
        UserRepositoryInterface $userRepository,
        CommentRepositoryInterface $commentRepository,
        StudentRepositoryInterface $studentRepository,
        CategoryLmsRepositoryInterface $categoryRepository,
    ) {
        $this->courseRepository = $courseRepository;
        $this->userRepository = $userRepository;
        $this->commentRepository = $commentRepository;
        $this->studentRepository = $studentRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function index()
    {
        $id = Auth('sanctum')->user()->id;
        $data = new stdClass();
        $minute = config('cache.mid_time')?? 720;
        //  $data->user = $this->userRepository->getById(Auth('sanctum')->user());
        $data->user = Cache::remember('user_' . $id, $minute, function () use ($id) {
            return $this->userRepository->getById(Auth('sanctum')->user());
        });

        $data->progress = Cache::remember('user_progress_courses_' . $id, $minute, function () use ($id) {
            return $this->studentRepository->getUserProgressCourses($id);
        });

        $data->done = Cache::remember('user_done_courses_' . $id, $minute, function () use ($id) {
            return $this->studentRepository->getUserDoneCourses($id);
        });

        $data->necessary = Cache::remember('necessary_courses', $minute, function () {
            return $this->studentRepository->getNecessaryCourses();
        });

        $data->categories = Cache::remember('categories_with_courses', $minute, function () {
            return $this->categoryRepository->getCategoriesWithCourses();
        });

        $data->bookmarks = Cache::remember('user_bookmarked_courses_' . $id, $minute, function () use ($id) {
            return $this->studentRepository->getUserBookmarkedCourses($id);
        });

        $data->comments = Cache::remember('star_comments', $minute, function () {
            return $this->commentRepository->getStarComments();
        });

        $data->recentlyVisitedCourses = Cache::remember('user_recently_visited_courses_' . $id, $minute, function () use ($id) {
            return $this->studentRepository->getUserRecentlyVisited($id);
        });

        $data->topCourses = Cache::remember('top_courses', $minute, function () {
            return $this->courseRepository->getTopCourses();
        });
        return response()->json(new UserHomeResource($data));
    }

    public function instructors(Request $request)
    {
        $instructors = $this->courseRepository->getInstructors($request);
        // return $instructors;
        return new InstructorsResource($instructors);
    }

    public function getInstructorCommonList()
    {
        $instructors = $this->courseRepository->getAllInstructors();

        $count = [
            'all' => $instructors->count(),
            'active' => $instructors->where('status', 'active')->count(),
            'notActive' => $instructors->where('status', '!=', 'active')->count(),
        ];

        $status = ['active', 'notActive'];

        return [
            'counts' => $count,
            'status' => $status,
        ];
    }
}
