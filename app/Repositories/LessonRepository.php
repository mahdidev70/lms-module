<?php

namespace TechStudio\Lms\app\Repositories;

use Dflydev\DotAccessData\Data;
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
            ->where('user_id', $userId)->latest()->first();
    }

    public function createUpdate($data)
    {
        $information = json_encode($data['trueAnswers']);
        
        $lesson = Lesson::updateOrCreate(
            ['id' => $data['id']],
            [
                'title' => $data['title'],
                'slug' => SlugGenerator::transform($data['title']),
                'chapter_id' => $data['chapterId'],
                'dominant_type' => $data['dominantType'],
                'content' => $data['content'],
                'information' => $information,
                'order' => $data['order'],
            ]
        );

        return $lesson;
    }
}
