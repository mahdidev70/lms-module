<?php

namespace TechStudio\Lms\app\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use TechStudio\Lms\app\Models\View;
use Illuminate\Support\Facades\Auth;
use TechStudio\Lms\app\Models\Skill;
use TechStudio\Lms\app\Models\Course;
use TechStudio\Core\app\Models\UserProfile;
use TechStudio\Lms\app\Models\CourseFeature;
use TechStudio\Core\app\Helper\SlugGenerator;
use App\Http\Resources\Lms\InstructorResource;
use TechStudio\Lms\app\Repositories\Interfaces\CourseRepositoryInterface;

class CourseRepository implements CourseRepositoryInterface
{
    function getBySlug($slug)
    {
        $courses = Course::where('slug', $slug)
            ->with('skills', 'instructor', 'category', 'chapters', 'comments')->where('status','published')->firstOrFail();
        return $courses;
    }

    function getById($id)
    {
        $courses = Course::where('id', $id)
            ->with('skills', 'instructor', 'category', 'chapters', 'comments')->firstOrFail();
        return $courses;
    }

    function all($request)
    {
        $courses = Course::when(
            $request->has('categoryId') && !empty($request->categoryId),
            function ($query) use ($request) {
                $query->where('category_id', $request->categoryId);
            }
        )->when(
            $request->has('skills') && count(json_decode($request->skills)) > 0,
            function ($query) use ($request) {
                $query->whereHas('skills', function ($q) use ($request) {
                    $q->whereIn('skill_id', json_decode($request->skills));
                });
            }
        )->when(
            $request->has('instructorId') && $request->has('instructorType')
                && !empty($request->instructorId) && !empty($request->instructorType),
            function ($query) use ($request) {
                if ($request->instructorType == 'User') {
                    $user = new UserProfile();
                    $type = get_class($user);
                }
                $query->where('instructor_id', $request->instructorId)->where('instructor_type', $type);
            }
        )->when($request->has('moreTime') && !empty($request->moreTime), function ($query) use ($request) {
            $query->where('total_duration', '>', $request->moreTime);
        })->when($request->has('lessTime') && !empty($request->lessTime), function ($query) use ($request) {
            $query->where('total_duration', '<', $request->lessTime);
        })->latest('publication_date')->paginate();

        return $courses;
    }

    public function getAllInstructors()
    {
        // if (auth()->check()) {
        //     $userId = Auth('sanctum')->user()->id;
        //     $instructors = Course::groupBy('instructor_id')->pluck('instructor_id');
        //     $instructors = UserProfile::whereIn('id', $instructors)->orWhere('user_id', $userId)->get();
        // }else {
        //     $instructors = Course::groupBy('instructor_id')->pluck('instructor_id');
        //     $instructors = UserProfile::whereIn('id', $instructors)->get();
        // }
        $instructors = UserProfile::get();
        return $instructors;
    }

    public function getInstructors($request)
    {
        $instructors = Course::groupBy('instructor_id')->pluck('instructor_id');
        $query = UserProfile::whereIn('user_id', $instructors)->with('courses');

        if ($request->filled('search')) {
            $txt = $request->get('search');

            $query->where(function ($q) use ($txt) {
                $q
                    ->orWhere('first_name', 'like', '%'.$txt.'%')
                    ->orWhere('last_name', 'like', '%'.$txt.'%');
            });
        }

        if ($request->has('sort')) {
            if ($request->sort == 'commentCount') {
                $instructors = $query->withCount(['courses as comment_count' => function ($query) {
                    $query->select('instructor_id')->join('core_comments', 'core_comments.commentable_id', '=', 'lms_courses.id')
                    ->where('lms_courses.instructor_id', '=', 'core_user_profiles.id');
                }])->orderBy('comment_count', 'desc')->paginate(10);
            } elseif ($request->sort == 'courseCount') {
                $instructors = $query->withCount('courses')->orderBy('courses_count', 'desc')->paginate(10);
            }
        } else {
            $instructors = $query->paginate(10);
        }

        return $instructors;
    }

    public function getTopCourses()
    {
        $result = DB::select('SELECT c.id, sum(s.rate) / COUNT(s.id) as "score"
        FROM lms_courses c
        join lms_students s
        on s.course_id = c.id
        GROUP BY c.id
        ORDER BY score DESC Limit 4');

        $coursesIds = [];
        foreach ($result as $item) {
            array_push($coursesIds, $item->id);
        }
        if(! sizeof($coursesIds) > 0){
            return [];
        }
        return Course::whereIn('id', $coursesIds)
            ->orderByRaw('FIELD (id, ' . implode(', ', $coursesIds) . ')')->get();
           // ->orderBy('publication_date', 'desc')->get();
    }

    public function getAllSkills()
    {
        return Skill::orderBy('created_at', 'desc')->get();
    }

    public function incrementField($courseId, $field)
    {
        Course::where('id', $courseId)->increment($field);
    }

    public function storeView($courseId, $userId)
    {
        View::updateOrInsert([
            'course_id' => $courseId,
            'user_id' => $userId
        ], [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    public function createUpdate($data)
    {
        $learningPoints = json_encode($data['learningPoints']);
        $features = json_encode($data['features']);
        $languages = json_encode($data['languages']);
        $supportItems = json_encode($data['supportItems']);
        $faq = json_encode($data['faq']);

        $instructorType = '';
        $instructorId = $data['instructor']['id'];

        $userModel = new UserProfile();
        $instructorType = get_class($userModel);

        $courseData = [
            'title' => $data['title'],
            'description' => $data['description'],
            'category_id' => $data['categoryId'],
            'banner_url' => $data['bannerUrl'],
            'banner_url_mobile' => $data['bannerUrlMobile'],
            'features' => $features,
            'languages' => $languages,
            'level' => $data['level'],
            'instructor_type' => $instructorType,
            'instructor_id' => $instructorId,
            'learning_points' => $learningPoints,
            'support_items' => $supportItems,
            'faq' => $faq,
            'prerequisites' => $data['prerequisites'] ? json_encode($data['prerequisites']) : null
        ];

        $course = Course::updateOrCreate(['id' => $data['id']], $courseData);

        if (isset($data['skillsId'])) {
            $course->skills()->sync($data['skillsId']);
        }

        return $course;
    }

    public function coursePreview($id)
    {
        $courses = Course::where('id', $id)
            ->with('skills', 'instructor', 'category', 'chapters', 'comments')
            ->firstOrFail();

        return $courses;
    }

    public function getAllFeatures()
    {
        return CourseFeature::all();
    }

    public function featureUpdateCreate($request)
    {
        return CourseFeature::updateOrCreate([
            'id' => $request->id
        ], [
            'title' => $request->title,
        ]);
    }

    public function featureDelete($request)
    {
        return CourseFeature::where('id', $request->id)->delete();
    }
    
    public function updateCourseEditeTime($request)
    {
        return  Course::withoutGlobalScopes()->where('id', $request)->update(['updated_at'=> Carbon::now()]);
    }
}
