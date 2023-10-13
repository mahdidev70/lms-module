<?php

namespace App\Repositories;

use App\Models\View;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\StudentRepositoryInterface;


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
        return $courses = View::where('user_id', Auth::user()->id)->with('course.instructor')
            ->take(4)->get()->pluck('course');
    }

    public function getNecessaryCourses(){
        return Course::where('necessary',1)->get();
    }
}
