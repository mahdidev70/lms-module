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
        return Course::whereIn('id', $courseIds)->orderBy('created_at', 'desc')->get();
    }


    public function getUserDoneCourses($userId)
    {
        $courseIds = Student::where('user_id', $userId)->where('in_roll', 'done')->pluck('course_id');
        return Course::whereIn('id', $courseIds)->orderBy('created_at', 'desc')->get();
    }

    public function getUserBookmarkedCourses($userId)
    {
        $courseIds = Student::where('user_id', $userId)->where('bookmark', 1)->pluck('course_id');
        return Course::whereIn('id', $courseIds)->orderBy('created_at', 'desc')->get();
    }

    public function getUserRecentlyVisited()
    {
        return View::where('user_id', Auth::user()->id)->with('course.instructor')
            ->orderBy('created_at', 'desc')->take(4)->get()->pluck('course');
    }

    public function getNecessaryCourses()
    {
        return Course::where('necessary',1)->orderBy('created_at', 'desc')->get();
    }

    public function getStudentList($request)
    {
       $students = self::getStudentListMainQuery($request)->paginate(10);
        return $students;
    }

    public function certificatesByStudent($request)
    {
        $query = Student::join(app(UserProfile::class)->getTable(), app(Student::class)->getTable() .'.user_id', '=', app(UserProfile::class)->getTable().'.user_id')
        ->whereNotNull(app(Student::class)->getTable().'.certificate_file')
            ->with('course', 'userProfile');
        if ($request->filled('search')) {
            $txt = $request->get('search');
            $query->where(function ($q) use ($txt) {
                $q->where(app(UserProfile::class)->getTable().'.first_name', 'like', '%'.$txt.'%')
                    ->orWhere(app(UserProfile::class)->getTable().'.last_name', 'like', '%'.$txt.'%');
            });
        }

        $students = $query->orderBy(app(Student::class)->getTable().'.created_at', 'desc')->paginate(10);
        return $students;
    }

    public function getStudentListExcel($request)
    {
        return self::getStudentListMainQuery($request)->get();
    }

    public function getStudentListMainQuery($request)
    {
        $query = UserProfile::query();

        $query->leftJoin(
            app(Student::class)->getTable(),
            app(UserProfile::class)->getTable() . '.id', '=',app(Student::class)->getTable() . '.user_id')
            ->select([
                app(UserProfile::class)->getTable() . '.id',
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
            ])->groupBy(app(UserProfile::class)->getTable() . '.id');

        if ($request->filled('search')) {
            $txt = $request->get('search');
            $query->where(function ($q) use ($txt) {
                $q->orWhere(app(UserProfile::class)->getTable().'.first_name', 'like', '% '.$txt.'%')
                    ->orWhere(app(UserProfile::class)->getTable().'.last_name', 'like', '% '.$txt.'%');
            });
        }
        if ($request->filled('filter')) {
            $filterTxt = $request->get('filter');
            $query->where(function ($q) use ($filterTxt) {
                $q->where(app(Student::class)->getTable().'.in_roll', 'like', '% '.$filterTxt.'%');
            });
        }
        return $query->latest(app(UserProfile::class)->getTable() .'.created_at');
    }

}
