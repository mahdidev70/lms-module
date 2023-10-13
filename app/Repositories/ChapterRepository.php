<?php

namespace App\Repositories;

use App\Helper\SlugGenerator;
use App\Models\Chapter;
use App\Repositories\Interfaces\ChapterRepositoryInterface;


class ChapterRepository implements ChapterRepositoryInterface
{
    public function getBySlug($slug)
    {
        return $chapter = Chapter::where(
            'slug',
            $slug
        )->with('course')->firstOrFail();
    }

    public function createUpdate($data)
    {
        $chapter = Chapter::updateOrCreate(
            ['id' => $data['id']],
            [
                'title' => $data['title'],
                'slug' => SlugGenerator::transform($data['title']),
                'description' => $data['description'],
                'course_id' => $data['courseId'],
                'order' => $data['order'],
            ]
            );

        return $chapter;
    }
}