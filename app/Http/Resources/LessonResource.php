<?php

namespace TechStudio\Lms\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;
use TechStudio\Core\app\Helper\PageContent;
use TechStudio\Lms\app\Http\Resources\ChapterResource;
use TechStudio\Lms\app\Http\Resources\CoursePreviewResource;
use TechStudio\Lms\app\Models\QuizParticipant;
use TechStudio\Lms\app\Models\UserLessonProgress;

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

        $information = json_decode($this->information);
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'dominantType' => $this->dominant_type,
            'isCompleted' => $userStatus,
            'currentUserStartTime' => $startTime,
            'timeLimitMinutes' => 60,
            'content' => $this->content,
            'order' => $this->order,
            'trueAnswers' => $information,
            'userQuizStatus' => $quizStatus,
            'score' => $score,
            'course'    => new CoursePreviewResource($this->chapter->course),
            'chapter' => new ChapterResource($this->chapter),
            'informations' => $this->informations,
            'textDuration' => ($this->dominant_type == 'text') ? $this->duration : null,
            'videoDuration' => ($this->dominant_type == 'video') ? $this->duration : null,
            'examDuration' => ($this->dominant_type == 'exam') ? $this->duration : null,
            'dominantType' => $this->dominant_type,
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
