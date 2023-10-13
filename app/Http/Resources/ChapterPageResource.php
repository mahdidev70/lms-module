<?php

namespace App\Http\Resources\Lms;

use App\Helper\PageContent;
use App\Models\Chapter;
use Illuminate\Http\Request;
use App\Models\UserLessonProgress;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;

class ChapterPageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $remaining = $this->remainingOfCourse($this->id);
        return [
            'sidebar' => new CourseSidebarResource($this->course),
            'title' => $this->title,
            'slug' => $this->slug,
            'remainingVideoDuration' => $remaining['remainVideoTime'],
            'remainingQuizQuestionsCount' => $remaining['remainingExamQuestions'],
            'remainingMinutesToRead' => $remaining['remainTextTime'],
            'totalMinutesToRead' => $remaining['allTextTime'],
            'totalVideoDuration' => $remaining['allVideoTime'],
            'totalQuizQuestionsCount' => $remaining['allExamQuestions'],
            'description' => $this->description,
            'course' => new CoursePreviewResource($this->course),
            'lessons' =>  LessonResource::collection($this->lessons),
        ];
    }

    function remainingOfCourse($chapterId)
    {
        $result = Chapter::whereId($chapterId)->with('lessons')->first();
        $lessons = $result->lessons;
        $lessonsIds = $result->lessons->pluck('id')->flatten()->toArray();
        $userLessonProgress = UserLessonProgress::where([
            ['user_id', Auth::user()->id], ['progress', 1]
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
                        array_push($remainingText, $lesson['content']);
                    }
                    array_push($allText, $lesson['content']);
                    break;
                case 'video':
                    if (!in_array(
                        $lesson['id'],
                        $userLessonProgress->toArray()
                    )) {
                        array_push($remainingVideo, $lesson['content']);
                    }
                    array_push($allVideo, $lesson['content']);
                    break;
                case 'exam':
                    if (!in_array(
                        $lesson['id'],
                        $userLessonProgress->toArray()
                    )) {
                        array_push($remainingExam, $lesson['content']);
                    }
                    array_push($allExam, $lesson['content']);
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
            $calculate = new PageContent($text);
            $remainTextTime += $calculate->getMinutesToRead();
        }

        foreach ($remainingVideo as $video) {
            $calculate = new PageContent($video);
            $remainVideoTime += $calculate->getVideosDuration();
        }

        foreach ($remainingExam as $exam) {
            $calculate = new PageContent($exam);
            $remainingExamQuestions += $calculate->getQuestionsCount();
        }

        foreach ($allText as $text) {
            $calculate = new PageContent($text);
            $allTextTime += $calculate->getMinutesToRead();
        }

        foreach ($allVideo as $video) {
            $calculate = new PageContent($video);
            $allVideoTime += $calculate->getVideosDuration();
        }

        foreach ($allExam as $exam) {
            $calculate = new PageContent($exam);
            $allExamQuestions += $calculate->getQuestionsCount();
        }

        // for demo testing 
        $remainVideoTime = rand(10,100);
        $remainTextTime = rand(10,100);
        $remainingExamQuestions = rand(10,100);

        $allTextTime = $remainTextTime+23;
        $allVideoTime = $remainVideoTime + 26;
        $allExamQuestions = $remainingExamQuestions+34;

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
