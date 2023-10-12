<?php

namespace App\Http\Controllers\Lms;

use stdClass;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Lms\UserHomeResource;
use App\Http\Resources\Lms\InstructorResource;
use App\Http\Resources\Lms\InstructorsResource;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\Interfaces\CommentRepositoryInterface;
use App\Repositories\Interfaces\StudentRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

class UserController extends Controller
{
    private UserRepositoryInterface $userRepository;
    private CourseRepositoryInterface $courseRepository;
    private StudentRepositoryInterface $studentRepository;
    private CommentRepositoryInterface $commentRepository;
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(
        CourseRepositoryInterface $courseRepository,
        UserRepositoryInterface $userRepository,
        CommentRepositoryInterface $commentRepository,
        StudentRepositoryInterface $studentRepository,
        CategoryRepositoryInterface $categoryRepository,
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
        $data->user = $this->userRepository->getById($id);
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

        $search = $request->get('search');
        
        $instructors = $this->courseRepository->getInstructors();

        $instructorsCollection = InstructorResource::collection($instructors);

        $displayNames = $instructorsCollection->pluck('displayName');

        return $instructorsCollection;

    }

    public function getCommonList()
    {
        //TODO    
    }
}
