<?php

namespace TechStudio\Lms\app\Repositories;

use Exception;
use App\Jobs\ProcessVideo;
use App\Jobs\ConvertVideo;
use Dflydev\DotAccessData\Data;
use Illuminate\Support\Facades\Log;
use TechStudio\Core\app\Helper\SlugGenerator;
use TechStudio\Lms\app\Models\Lesson;
use TechStudio\Lms\app\Models\QuizParticipant;
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

    public function getLastQuizResult($lessonId,$userId)
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
                'slug' => $data['slug'] ? $data['slug'] : SlugGenerator::transform($data['title']),
                'chapter_id' => $data['chapterId'],
                'dominant_type' => $data['dominantType'],
                'content' => $data['content'],
                'information' => $information,
                'order' => $data['order'],
            ]
        );
        Log::info("befor dispach job");
        $videoId = null;
        if(
            isset($data->content[0]) && 
            isset($data->content[0]['content']) && 
            isset($data->content[0]['content']['url'])
            )
        {
            $videoId = $data->content[0]['content']['url'];
        }

        Log::info($videoId);
        if (
            $data->dominantType == 'video' &&
            $videoId != null &&
            !filter_var($videoId, FILTER_VALIDATE_URL)
        ) {
            Log::info("when job process dispach");
            ConvertVideo::dispatch($lesson, $videoId,$data['title']);
            // ProcessVideo::dispatch($lesson, $videoId);
        }
        return $lesson;
    }
}
