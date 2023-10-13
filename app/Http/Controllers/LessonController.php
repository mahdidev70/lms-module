<?php

namespace TechStudio\Lms\app\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lms\LessonCreateUpdateRequest;
use App\Http\Resources\Lms\LessonPageResource;
use App\Repositories\Interfaces\LessonRepositoryInterface;

class LessonController extends Controller
{
    private LessonRepositoryInterface $repository;

    public function __construct(LessonRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function show($lessonSlug)
    {
        $lesson = $this->repository->getBySlug($lessonSlug);

        return response()->json(new LessonPageResource($lesson));
    }

    public function list()
    {
        return 'aaadd';
    }

    public function editCreateLesson(LessonCreateUpdateRequest $lessonCreateUpdateRequest)
    {
        $lesson = $this->repository->createUpdate($lessonCreateUpdateRequest);
        
        new LessonPageResource($lesson);

        return $lesson->id;
    }
}
