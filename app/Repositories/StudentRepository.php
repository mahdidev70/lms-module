<?php

namespace TechStudio\Lms\app\Repositories;


use Illuminate\Support\Facades\Auth;
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
        $query = Student::with('course', 'userProfile')->join('user_profiles', 'students.user_id', '=', 'user_profiles.id')
        ->groupBy('students.user_id')->selectRaw('count(*) as total, students.user_id');

        if ($request->filled('search')) {

            $txt = $request->get('search');

            $query->where(function ($q) use ($txt) {
                $q->where('user_profiles.first_name', 'like', '%'.$txt.'%')
                    ->orWhere('user_profiles.last_name', 'like', '%'.$txt.'%');
            });
        }

        $students = $query->paginate(10);
        return $students;
    }

    public function certificatesByStudent($request) 
    {
        $query = Student::whereNotNull('certificate_file')->join('user_profiles', 'students.user_id', '=', 'user_profiles.id')
        ->with('course', 'userProfile');

        if ($request->filled('search')) {

            $txt = $request->get('search');

            $query->where(function ($q) use ($txt) {
                $q->where('user_profiles.first_name', 'like', '%'.$txt.'%')
                    ->orWhere('user_profiles.last_name', 'like', '%'.$txt.'%');
            });
        }

        $students = $query->paginate(10);
        return $students;
    }
}
