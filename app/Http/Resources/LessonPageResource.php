<?php

namespace TechStudio\Lms\app\Http\Resources;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;
use stdClass;
use TechStudio\Core\app\Helper\PageContent;
use TechStudio\Lms\app\Http\Resources\ChapterResource;
use TechStudio\Lms\app\Http\Resources\CoursePreviewResource;
use TechStudio\Lms\app\Http\Resources\CourseSidebarResource;
use TechStudio\Lms\app\Models\Chapter;
use TechStudio\Lms\app\Models\Lesson;
use TechStudio\Lms\app\Models\QuizParticipant;
use TechStudio\Lms\app\Models\UserLessonProgress;

class LessonPageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $startTime = null;
        $quizStatus = 'not_started';
        $score = null;
        if ($this->dominant_type == 'exam') {
            $result = QuizParticipant::where('lesson_id', $this->id)
                ->where('user_id', Auth::user()->id)->latest()->first();
            if ($result) {
                $startTime = $result->created_at;
                $quizStatus = $result->status;
                $score = $result->score;
            }
        }
        $userProgress = UserLessonProgress::where('user_id', Auth::user()->id)
            ->where('lesson_id', $this->id)->latest()->first();
        $userStatus = (isset($userProgress)) ? $userProgress->progress : 0;

        $previous = null;
        $next = null;
        $chapterIds = Chapter::where('course_id', $this->chapter->course_id)->pluck('id');
        if ($this->order != 1) {
            $previousLesson = Lesson::whereIn('chapter_id', $chapterIds)->where('order', $this->order - 1)->first();
            if ($previousLesson) {
                $previous = $previousLesson;
            }
        }
        $nextLesson = Lesson::whereIn('chapter_id', $chapterIds)->where('order', $this->order + 1)->first();
        if ($nextLesson) {
            $next = $nextLesson;
        }
        $examAllowed = false;
        $startedCarbon =Carbon::parse($startTime);
        if(
            $quizStatus == 'not_started' || $quizStatus == 'fail' &&  
            $startedCarbon->lt(Carbon::now()->subMinutes(60)))
            {
            $examAllowed = true;
        }

        return [
            'sidebar' => new CourseSidebarResource($this->chapter->course),
            'title' => $this->title,
            'id' => $this->id,
            'slug' => $this->slug,
            'dominantType' => $this->dominant_type,
            'isCompleted' => $userStatus,
            'currentUserStartTime' => $startTime,
            'examAllowed' => $examAllowed,
            'userQuizStatus' => $quizStatus,
            'score' => $score,
            'timeLimitMinutes' => 60,
            'content' => $this->content,
            'order' => $this->order,
            'course' => new CoursePreviewResource($this->chapter->course),
            'chapter' => new ChapterResource($this->chapter),
            'informations' => $this->informations,
            'textDuration' => ($this->dominant_type == 'text') ? $this->duration : null,
            'videoDuration' => ($this->dominant_type == 'video') ? $this->duration : null,
            'examDuration' => ($this->dominant_type == 'exam') ? $this->duration : null,
            'next' => isset($next->slug) ? [
                'slug' => $next->slug,
                'id' => $next->id
            ] : null,
            'previous' => isset($previous->slug) ? [
                'slug' => $previous->slug,
                'id' => $previous->id
            ] : null
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
}
