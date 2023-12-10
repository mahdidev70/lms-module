<?php

namespace TechStudio\Lms\app\Repositories;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use TechStudio\Lms\app\Models\Course;
use TechStudio\Lms\app\Models\Student;
use TechStudio\Lms\app\Models\View;
use TechStudio\Lms\app\Repositories\Interfaces\StudentRepositoryInterface;

class StudentRepository implements StudentRepositoryInterface
{
    public function getUserCourses($userId)
    {
        return Student::where('user_id', $userId)->with('course')->get();
    }

    public function getUserInrolledCourses($userId)
    {
        $courseIds = Student::where('user_id', $userId)->whereNotNull('in_roll')->pluck('course_id');
        return Course::whereIn('id', $courseIds)->get();
    }


    public function getUserProgressCourses($userId)
    {
        $courseIds = Student::where('user_id', $userId)->where('in_roll', 'progress')->pluck('course_id');
        return Course::whereIn('id', $courseIds)->get();
    }


    public function getUserDoneCourses($userId)
    {
        $courseIds = Student::where('user_id', $userId)->where('in_roll', 'done')->pluck('course_id');
        return Course::whereIn('id', $courseIds)->get();
    }

    public function getUserBookmarkedCourses($userId)
    {
        $courseIds = Student::where('user_id', $userId)->where('bookmark', 1)->pluck('course_id');
        return Course::whereIn('id', $courseIds)->get();
    }

    public function getUserRecentlyVisited()
    {
        return View::where('user_id', Auth::user()->id)->with('course.instructor')
            ->take(4)->get()->pluck('course');
    }

    public function getNecessaryCourses()
    {
        return Course::where('necessary',1)->get();
    }

    public function getStudentList($request) 
    {
        $query = Student::with('course', 'userProfile')
            ->groupBy('lms_students.user_id')
            ->selectRaw('count(*) as total, lms_students.user_id')
            ;

        $sortOrder= 'desc';
        if (isset($request->sortOrder) && ($request->sortOrder ==  'asc' || $request->sortOrder ==  'desc')) {
            $sortOrder = $request->sortOrder;
        }

        if ($request->has('sortKey')) {
            if ($request->sortKey == 'requireCourseCount') {
                $query->withCount(['course as necessary_sum' => function ($query) {
                    $query->select(DB::raw('sum(necessary)'));
                }])->orderBy('nessecery_sum', $sortOrder);
            }elseif ($request->sortKey == 'bookmarkCourseCount') {
                $query->orderBy('bookmark', $sortOrder);
            }elseif ($request->sortKey == 'complitedCourseCount') {
                $query->where('in_roll', 'done')->orderBy('in_roll', $sortOrder);
            }elseif ($request->sortKey == 'notComplitedCourseCount') {
                $query->where('in_roll', 'done')->orderBy('in_roll', $sortOrder);
            }
        }

        if ($request->filled('search')) {
            $txt = $request->get('search');
            $query->where(function ($q) use ($txt) {
                $q->where('core_user_profiles.first_name', 'like', '%'.$txt.'%')
                    ->orWhere('core_user_profiles.last_name', 'like', '%'.$txt.'%');
            });
        }

        $students = $query->paginate(10);
        return $students;
    }

    public function certificatesByStudent($request) 
    {
        $query = Student::whereNotNull('certificate_file')->join('core_user_profiles', 'lms_students.user_id', '=', 'core_user_profiles.id')
        ->with('course', 'userProfile');

        if ($request->filled('search')) {
            $txt = $request->get('search');
            $query->where(function ($q) use ($txt) {
                $q->where('core_user_profiles.first_name', 'like', '%'.$txt.'%')
                    ->orWhere('core_user_profiles.last_name', 'like', '%'.$txt.'%');
            });
        }

        $students = $query->paginate(10);
        return $students;
    }
}
