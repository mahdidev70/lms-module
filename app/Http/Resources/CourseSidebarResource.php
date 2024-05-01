<?php

namespace TechStudio\Lms\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TechStudio\Lms\app\Models\Course;
use TechStudio\Core\app\Helper\PageContent;
use TechStudio\Lms\app\Services\Calculator;
use Illuminate\Http\Resources\Json\JsonResource;
use TechStudio\Lms\app\Models\UserLessonProgress;

class CourseSidebarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $calculatorResult = Calculator::courseProgress($this->id);

        return [
            'courseTitle' => $this->title,
            'slug' => $this->slug,
            'courseProgress' => $calculatorResult['passedPercentage'] ?? null,
            'completedDate' => $calculatorResult['completedDate'] ?? null,
            'chapters' => $this->chapters->map(fn ($chapter) => [
                'title' => $chapter->title,
                'slug' => $chapter->slug,
                'lessons' => $chapter->lessons->map(fn ($lesson) => [
                    'title' => $lesson->title,
                    'slug' => $lesson->slug,
                    'dominantType' => $lesson->dominant_type,
                    'minutesToRead' => ($lesson->dominant_type == 'text') ? $this->getTextTime($lesson) : null,
                    'duration' => ($lesson->dominant_type == 'video') ? $this->getVideoTime($lesson) : null,
                    'numberOfQuestions' => ($lesson->dominant_type == 'exam') ? $this->getQuestionCount($lesson) : null,
                    'isCompleted' => ($this->isCompleted($lesson) == true) ? 1 : 0,
                ]),
            ]),
        ];
    }


    function getTextTime($lesson): int
    {
        $calculate = new PageContent($lesson->content);
        return $calculate->getMinutesToRead();
    }

    function getVideoTime($lesson): int
    {
        $calculate = new PageContent($lesson->content);
        return $calculate->getVideosDuration();
    }

    function getQuestionCount($lesson): int
    {
        $calculate = new PageContent($lesson->content);
        return $calculate->getQuestionsCount();
    }

    function isCompleted($lesson)
    {
        $isCompleted = UserLessonProgress::where([
            ['lesson_id', $lesson->id], ['user_id', Auth::user()->id], ['progress', 1]
        ])->first();
        return (bool) $isCompleted;
    }

    function getTotalProgress($courseId) 
    {
        $result = Course::find($courseId)->with('chapters.lessons')->get();
        $lessons = $result->pluck('chapters.*.lessons.*')->flatten()->toArray();
        $lessonsIds = $result->pluck('chapters.*.lessons.*.id')->flatten()->toArray();

        $userLessonProgress = UserLessonProgress::where([
            ['user_id', Auth::user()->id], ['progress', '>', 0]
        ])->whereIn(
            'lesson_id',
            $lessonsIds
        )->pluck('progress');

        $lessonProgress = 0 ;
        foreach($userLessonProgress as $lessonScore){
            $lessonProgress += $lessonScore;
        }
        if($lessonProgress > 0 && count($lessons) > 0)
        {
            return number_format( $lessonProgress / count($lessons) * 100 );
        }
        return 0;    }
}
