<?php

namespace App\Http\Resources\Lms;

use App\Helper\PageContent;
use App\Models\Chapter;
use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Models\QuizParticipant;
use App\Models\UserLessonProgress;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;
use stdClass;

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
            'currentUserStatus' => $userStatus,
            'currentUserStartTime' => $startTime,
            'examAllowed' => $examAllowed,
            'userQuizStatus' => $quizStatus,
            'score' => $score,
            'timeLimitMinutes' => 60,
            'content' => $this->content,
            'course' => new CoursePreviewResource($this->chapter->course),
            'chapter' => new ChapterResource($this->chapter),
            'informations' => $this->informations,
            'textDuration' => ($this->dominant_type == 'text') ? $this->getTextTime($this) : null,
            'videoDuration' => ($this->dominant_type == 'video') ? $this->getVideoTime($this) : null,
            'examDuration' => ($this->dominant_type == 'exam') ? $this->getQuestionCount($this) : null,
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
