<?php

namespace TechStudio\Lms\app\Services;

use Exception;
use TechStudio\Lms\app\Models\Lesson;
use TechStudio\Lms\app\Models\Chapter;
use TechStudio\Lms\app\Models\UserLessonProgress;

class Calculator
{
    public static function courseProgress($courseId)
    {
        $chaptersId = Chapter::where('course_id', $courseId)->pluck('id');
        $lessonsId = Lesson::whereIn('chapter_id', $chaptersId)->pluck('id');

        $passedCount = null;
        $passedPercentage = 0;
        $touchPointLesson = null;
        $id = null;
        try {
            $id = Auth('sanctum')->user()->id;
        } catch (Exception $e) {
        }
        if ($id && count($lessonsId) > 0) {
            $passedIds = UserLessonProgress::where('user_id', $id)->whereIn('lesson_id', $lessonsId)->pluck('id');

            $chaptersId = Chapter::where('course_id', $courseId)->pluck('id');
            $lessonsId = Lesson::whereIn('chapter_id', $chaptersId)->pluck('id');
            $passedIds = UserLessonProgress::where('user_id', Auth('sanctum')->id())
                ->whereIn('lesson_id', $lessonsId)->pluck('lesson_id');
            $unPassedIds = $lessonsId->diff($passedIds);
            $touchPointLesson = Lesson::with('chapter')->whereIn('id', $unPassedIds->values())
                ->orderBy('order', 'asc')->first();

            $passedCount = count($passedIds);
            if ($passedCount > 0 && count($lessonsId) > 0) {
                $passedPercentage =  floor($passedCount / count($lessonsId) * 100);
            }
        }
        return [
            'lessonsCount' => count($lessonsId),
            'passedCount' => $passedCount,
            'passedPercentage' => $passedPercentage,
            'touchPointLesson' => $$touchPointLesson
        ];
    }
}
