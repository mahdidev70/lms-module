<?php

namespace TechStudio\Lms\app\Repositories;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use TechStudio\Core\app\Models\UserProfile;
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
        $query = UserProfile::query();

        $query->leftJoin(
        app(Student::class)->getTable(),
        app(UserProfile::class)->getTable() . '.id', '=',app(Student::class)->getTable() . '.user_id') ->select([
            app(Student::class)->getTable() . '.id',
            app(Student::class)->getTable() . '.user_id',
            app(UserProfile::class)->getTable() . '.first_name',
            app(UserProfile::class)->getTable() . '.last_name',
            app(UserProfile::class)->getTable() . '.avatar_url',
            DB::raw('sum(CASE WHEN '
                . app(Student::class)->getTable() . ".in_roll = 'progress' THEN 1 ELSE 0 END) as progressCount"),
            DB::raw('sum(CASE WHEN '
                . app(Student::class)->getTable() . ".in_roll = 'done' THEN 1 ELSE 0 END) as doneCount"),
            DB::raw('COALESCE(sum( '
                . app(Student::class)->getTable() . '.bookmark ), 0) as bookmarkCount')
        ])->groupBy(app(UserProfile::class)->getTable() . '.id')->orderBy('id', 'DESC')->get();

        if ($request->filled('search')) {
            $txt = $request->get('search');
            $query->where(function ($q) use ($txt) {
                $q->orWhere(app(UserProfile::class)->getTable().'.first_name', 'like', '% '.$txt.'%')
                ->orWhere(app(UserProfile::class)->getTable().'.last_name', 'like', '% '.$txt.'%');
            });
        }

        $students = $query->paginate(10);
        return $students;
    }

    public function certificatesByStudent($request)
    {
        $query = Student::whereNotNull('certificate_file')->join('core_user_profiles', 'lms_students.user_id', '=', 'core_user_profiles.user_id')
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
