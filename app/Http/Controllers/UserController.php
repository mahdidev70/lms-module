<?php

namespace TechStudio\Lms\app\Http\Controllers;

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
        $id = Auth::user()->id;
        $data = new stdClass();
        $data->user = $this->userRepository->getById(Auth::user());
        $data->progress = $this->studentRepository->getUserProgressCourses($id);
        $data->done = $this->studentRepository->getUserDoneCourses($id);
        $data->necessary = $this->studentRepository->getNecessaryCourses();
        $data->categories = $this->categoryRepository->getCategoriesWithCourses();
        $data->bookmarks = $this->studentRepository->getUserBookmarkedCourses($id);
        $data->comments = $this->commentRepository->getStarComments();
        $data->recentlyVisitedCourses = $this->studentRepository->getUserRecentlyVisited();
        $data->topCourses = $this->courseRepository->getTopCourses();

        return response()->json(new UserHomeResource($data));
    }

    public function instructors(Request $request)
    {
        $instructors = $this->courseRepository->getInstructors($request);
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
