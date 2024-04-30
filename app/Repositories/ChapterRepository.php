<?php

namespace TechStudio\Lms\app\Repositories;

use TechStudio\Core\app\Helper\SlugGenerator;
use Illuminate\Support\Facades\Auth;
use TechStudio\Lms\app\Models\Chapter;
use TechStudio\Lms\app\Models\Student;
use TechStudio\Lms\app\Repositories\Interfaces\ChapterRepositoryInterface;

// use TechStduio\Lms\app\Repositories\Interface

class ChapterRepository implements ChapterRepositoryInterface
{
    public function getBySlug($slug)
    {
        $chapter = Chapter::where('slug', $slug)->with('course')->firstOrFail();
        if ($chapter->course == null || $chapter->course->status != 'published') {
            return abort(404, 'Chapter Not Found!');
        }

        if (
            Auth('sanctum')->id() > 0 &&
            ! Student::where('user_id', $user->id)->where('course_id', $chapter->course_id)->whereNotNull('in_roll')->first()
        ){
            Student::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => $chapter->course_id,
                ],
                [
                    'user_id' => $user->id,
                    'course_id' => $chapter->course_id,
                    'in_roll' => 'progress',
                ]
            );
        }
        return $chapter;
    }

    public function createUpdate($data)
    {
        $chapter = Chapter::updateOrCreate(
            ['id' => $data['id']],
            [
                'title' => $data['title'],
                'slug' => $data['slug'] ? $data['slug'] : SlugGenerator::transform($data['title']),
                'description' => $data['description'],
                'course_id' => $data['courseId'],
                'order' => $data['order'],
            ]
        );

        return $chapter;
    }

    public function preview($slug)
    {
        $chapter = Chapter::where('slug', $slug)->with('course')->firstOrFail();

        return $chapter;
    }

    public function getCourseChaptersId($request)
    {
        return $chapters = Chapter::where('course_id', $request)->pluck('id');
    }
}
