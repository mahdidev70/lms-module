<?php

namespace TechStudio\Lms\app\Repositories;

use Exception;
use Carbon\Carbon;
use App\Jobs\ConvertVideo;
use App\Jobs\ProcessVideo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use TechStudio\Lms\app\Models\Lesson;
use TechStudio\Lms\app\Models\Chapter;
use TechStudio\Lms\app\Models\Student;
use TechStudio\Core\app\Helper\SlugGenerator;
use TechStudio\Lms\app\Models\QuizParticipant;
use TechStudio\Lms\app\Models\UserLessonProgress;
use TechStudio\Lms\app\Repositories\Interfaces\LessonRepositoryInterface;

class LessonRepository implements LessonRepositoryInterface
{
    public function getBySlug($slug)
    {
        return $lesson = Lesson::where('slug', $slug)->with(
            'chapter',
            'chapter.course'
        )->firstOrFail();
    }

    public function getQuizById($id)
    {
        return Lesson::where('id', $id)->where('dominant_type', 'exam')->firstOrFail();
    }

    public function storeQuizResult($request)
    {
        return QuizParticipant::create($request);
    }

    public function getLastQuizResult($lessonId, $userId)
    {
        return QuizParticipant::where('lesson_id', $lessonId)
            ->where('user_id', $userId)->latest('created_at')->first();
    }

    public function createUpdate($data)
    {
        $information = json_encode($data['trueAnswers']);

        $lesson = Lesson::updateOrCreate(
            ['id' => $data['id']],
            [
                'title' => $data['title'],
                'chapter_id' => $data['chapterId'],
                'dominant_type' => $data['dominantType'],
                'content' => $data['content'],
                'information' => $information,
                'order' => $data['order'],
                'duration' => $data['duration']
            ]
        );

        $videoId = null;
        if (
            isset($data->content[0]) &&
            isset($data->content[0]['content']) &&
            isset($data->content[0]['content']['url'])
        ) {
            $videoId = $data->content[0]['content']['url'];
        }


        if (
            $data->dominantType == 'video' &&
            $videoId != null &&
            !filter_var($videoId, FILTER_VALIDATE_URL)
        ) {
            Log::info("when job process dispach");
            ConvertVideo::dispatch($lesson, $videoId, $data['title']);
            // ProcessVideo::dispatch($lesson, $videoId);
        }
        return $lesson;
    }


    public function incrementOrders($chaptersId, $order)
    {
        return Lesson::whereIn('chapter_id',  $chaptersId)->where('order', '>=', $order)
            ->update(['order' => DB::raw('`order` + 1')]);
    }

    public function decrementOrders($chaptersId, $order)
    {
        return Lesson::whereIn('chapter_id',  $chaptersId)->where('order', '>=', $order)
            ->update(['order' => DB::raw('`order` - 1')]);
    }

    public function updateOrders($request)
    {
        foreach ($request as $lesson) {
            Lesson::where('id', $lesson['id'])->update(['order' => $lesson['order']]);
        }
        return true;
    }

    public function updateTouchPoint($request)
    {
        UserLessonProgress::updateOrInsert(
            ['lesson_id' => $request, 'user_id' => Auth('sanctum')->id()],
            [
                'progress' => 1,
                'created_at' => Carbon::now()
            ]
        );
        
        $lesson = Lesson::with('chapter')->where('id', $request)->first();
        $chapters = Chapter::where('course_id', $lesson->chapter->course_id)->pluck('id');
        $lastLesson = Lesson::whereIn('chapter_id', $chapters)->orderBy('order', 'DESC')->first();
        if ($lastLesson->id == $request) {
            Student::updateOrCreate(
                [
                    'user_id' => Auth('sanctum')->id(),
                    'course_id' => $lesson->chapter->course_id,
                ],
                [
                    'user_id' =>  Auth('sanctum')->id(),
                    'course_id' => $lesson->chapter->course_id,
                    'in_roll' => 'done',
                    'comment' => ''
                ]
            );
        }
    }
}
