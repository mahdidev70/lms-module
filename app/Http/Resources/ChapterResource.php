<?php

namespace TechStudio\Lms\app\Http\Resources;

use App\Helper\PageContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;
use TechStudio\Lms\app\Models\Chapter;
use TechStudio\Lms\app\Models\UserLessonProgress;

class ChapterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $userId = Auth::user()->id ?? 1;
        $remaining = $this->remainingOfCourse($this->id,$userId);
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'remainingVideoDuration' => $remaining['remainVideoTime'],
            'remainingQuizQuestionsCount' => $remaining['remainingExamQuestions'],
            'remainingMinutesToRead' => $remaining['remainTextTime'],
            'totalMinutesToRead' => $remaining['allTextTime'],
            'totalVideoDuration' => $remaining['allVideoTime'],
            'totalQuizQuestionsCount' => $remaining['allExamQuestions'],
            'description' => $this->description,
        ];
    }

    function remainingOfCourse($chapterId,$userId)
    {
        $result = Chapter::whereId($chapterId)->with('lessons')->first();
        $lessons = $result->lessons;
        $lessonsIds = $result->lessons->pluck('id')->flatten()->toArray();
        $userLessonProgress = UserLessonProgress::where([
            ['user_id', $userId], ['progress', 1]
        ])->whereIn(
            'lesson_id',
            $lessonsIds
        )->pluck('id');

        $remainingText = [];
        $remainingVideo = [];
        $remainingExam = [];

        $allText = [];
        $allVideo = [];
        $allExam = [];

        foreach ($lessons as $lesson) {
            switch ($lesson['dominant_type']) {
                case 'text':
                    if (!in_array(
                        $lesson['id'],
                        $userLessonProgress->toArray()
                    )) {
                        array_push($remainingText, $lesson);
                    }
                    array_push($allText, $lesson);
                    break;
                case 'video':
                    if (!in_array(
                        $lesson['id'],
                        $userLessonProgress->toArray()
                    )) {
                        array_push($remainingVideo, $lesson);
                    }
                    array_push($allVideo, $lesson);
                    break;
                case 'exam':
                    if (!in_array(
                        $lesson['id'],
                        $userLessonProgress->toArray()
                    )) {
                        array_push($remainingExam, $lesson);
                    }
                    array_push($allExam, $lesson);
                    break;
                default:
                    Log::error('lesson' . $lesson['id'] . 'dosent have true type');
            }
        }

        $remainTextTime = 0;
        $remainVideoTime = 0;
        $remainingExamQuestions = 0;

        $allTextTime = 0;
        $allVideoTime = 0;
        $allExamQuestions = 0;

        foreach ($remainingText as $text) {
            // $calculate = new PageContent($text);
            // $remainTextTime += $calculate->getMinutesToRead();
            $remainTextTime += $text->duration;
        }

        foreach ($remainingVideo as $video) {
            // $calculate = new PageContent($video);
            // $remainVideoTime += $calculate->getVideosDuration();
            $remainVideoTime += $video->duration;
        }

        foreach ($remainingExam as $exam) {
            // $calculate = new PageContent($exam);
            // $remainingExamQuestions += $calculate->getQuestionsCount();
            $remainingExamQuestions += $exam->duration;
        }

        foreach ($allText as $text) {
            // $calculate = new PageContent($text);
            // $allTextTime += $calculate->getMinutesToRead();
            $allTextTime += $text->duration;
        }

        foreach ($allVideo as $video) {
            // $calculate = new PageContent($video);
            // $allVideoTime += $calculate->getVideosDuration();
            $allVideoTime += $video->duration;
        }

        foreach ($allExam as $exam) {
            // $calculate = new PageContent($exam);
            // $allExamQuestions += $calculate->getQuestionsCount();
            $allExamQuestions += $exam->duration;
        }

        return compact(
            'remainTextTime',
            'remainVideoTime',
            'remainingExamQuestions',
            'allTextTime',
            'allVideoTime',
            'allExamQuestions'
        );
    }
}
