<?php

namespace App\Repositories;

use App\Helper\ArrayPaginate;
use App\Helper\SlugGenerator;
use App\Http\Resources\Lms\InstructorResource;
use App\Models\Alias;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Category;
use App\Models\Skill;
use App\Models\UserProfile;
use App\Models\View;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use Carbon\Carbon;

class CourseRepository implements CourseRepositoryInterface
{
    function getBySlug($slug)
    {
        $courses = Course::where('slug', $slug)
            ->with('skills', 'instructor', 'category', 'chapters', 'comments')->firstOrFail();
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
                } else {
                    $alias = new Alias();
                    $type = get_class($alias);
                }
                $query->where('instructor_id', $request->instructorId)->where('instructor_type', $type);
            }
        )->when($request->has('moreTime') && !empty($request->moreTime), function ($query) use ($request) {
            $query->where('total_duration', '>', $request->moreTime);
        })->when($request->has('lessTime') && !empty($request->lessTime), function ($query) use ($request) {
            $query->where('total_duration', '<', $request->lessTime);
        })->latest()->paginate();

        return $courses;
    }

    public function getAllInstructors()
    {
        return $instructor = Course::get()->unique('instructor_type', 'instructor_id')->pluck('instructor');
    }

    public function getInstructors()
    {

        $userProfile = new UserProfile();
        $alias = new Alias();
        $userProfileIds = Course::select('instructor_id')->distinct()
            ->where('instructor_type', get_class($userProfile))
            ->pluck('instructor_id');

        $aliasIds = Course::select('instructor_id')->distinct()
            ->where('instructor_type', get_class($alias))
            ->pluck('instructor_id');

        $userProfiles = UserProfile::whereIn('id', $userProfileIds)->get();
        $aliases = Alias::whereIn('id', $aliasIds)
            ->withCount('courses')
            ->with('courses:id')
            ->get();

        $instructors = $aliases->merge($userProfiles);
        
        return $instructors;
    }

    public function getTopCourses()
    {
        $result = DB::select('SELECT c.id, sum(s.rate) / COUNT(s.id) as "score"  
        FROM courses c
        join students s 
        on s.course_id = c.id
        GROUP BY c.id 
        ORDER BY score DESC Limit 4');

        $coursesIds = [];
        foreach ($result as $item) {
            array_push($coursesIds, $item->id);
        }

        return Course::whereIn('id', $coursesIds)
            ->orderByRaw('FIELD (id, ' . implode(', ', $coursesIds) . ')')->get();
    }

    public function getAllSkills()
    {
        return Skill::get();
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
        $learningPoints = json_encode($data['learningPoint']);
        $features = json_encode($data['features']);
        $languages = json_encode($data['languages']);
        $supportItem = json_encode($data['supportItem']);
        $faq = json_encode($data['faq']);
    
        $instructorType = '';
        $instructorId = $data['instructor']['id'];
    
        if ($data['instructor']['type'] == 'User') {
            $instructorType = 'App\Models\UserProfile';
        } elseif ($data['instructor']['type'] == 'Alias') {
            $instructorType = 'App\Models\Alias';
        }
    
        $courseData = [
            'title' => $data['title'],
            'slug' => SlugGenerator::transform($data['title']),
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
            'support_items' => $supportItem,
            'FAQ' => $faq,
        ];
    
        $course = Course::updateOrCreate(['id' => $data['id']], $courseData);
    
        if (!empty($data['skillId'])) {
            $course->skills()->sync([$data['skillId']]);
        }

        // $course = Course::updateOrCreate(['id' => $data['id']], $courseData);
    
        // if (!empty($data['skillIds']) && is_array($data['skillIds']) && count($data['skillIds']) > 0) {
        //     $skills = collect($data['skillIds'])->map(function ($skillId) {
        //         return Skill::findOrFail($skillId);
        //     });
            
        //     $course->skills()->sync($skills->pluck('id')->toArray());

        // } else {
        //     $course->skills()->detach();
        // }
    
        return $course;
    }

}
