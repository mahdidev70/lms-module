<?php

namespace App\Http\Resources\Lms;

use App\Helper\PageContent;
use Illuminate\Http\Request;
use App\Models\QuizParticipant;
use App\Models\UserLessonProgress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
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
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'currentUserStatus' => $userStatus,
            'currentUserStartTime' => $startTime,
            'timeLimitMinutes' => 60,
            'content' => $this->content,
            'userQuizStatus' => $quizStatus,
            'score' => $score,
            'timeLimitMinutes' => 60,
            'course'    => new CoursePreviewResource($this->chapter->course),
            'chapter' => new ChapterResource($this->chapter),
            'informations' => $this->informations,
            'textDuration' => ($this->dominant_type == 'text') ? $this->getTextTime($this) : null,
            'videoDuration' => ($this->dominant_type == 'video') ? $this->getVideoTime($this) : null,
            'examDuration' => ($this->dominant_type == 'exam') ? $this->getQuestionCount($this) : null,
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
