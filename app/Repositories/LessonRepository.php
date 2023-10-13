<?php

namespace App\Repositories;

use App\Helper\SlugGenerator;
use App\Models\Lesson;
use App\Models\QuizParticipant;
use App\Repositories\Interfaces\LessonRepositoryInterface;
use Dflydev\DotAccessData\Data;

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

        $content = json_encode($data['content']);

        $information = json_encode($data['answers']);

        $lesson = Lesson::updateOrCreate(
            ['id' => $data['id']],
            [
                'title' => $data['title'],
                'slug' => SlugGenerator::transform($data['title']),
                'chapter_id' => $data['chapterId'],
                'dominant_type' => $data['dominantType'],
                'content' => $content,
                'information' => $information,
                'order' => $data['order'],
            ]
        );

        return $lesson;
    }
}
