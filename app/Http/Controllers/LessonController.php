<?php

namespace TechStudio\Lms\app\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use TechStudio\Blog\app\Models\Article;
use TechStudio\Lms\app\Http\Requests\LessonCreateUpdateRequest;
use TechStudio\Lms\app\Http\Resources\LessonPageResource;
use TechStudio\Lms\app\Http\Resources\LessonResource;
use TechStudio\Lms\app\Models\Lesson;
use TechStudio\Lms\app\Repositories\Interfaces\LessonRepositoryInterface;

class LessonController extends Controller
{
    private LessonRepositoryInterface $repository;

    public function __construct(LessonRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function show($local, $lessonSlug)
    {
        $lesson = $this->repository->getBySlug($lessonSlug);
        return response()->json(new LessonPageResource($lesson));
    }

    public function editCreateLesson(LessonCreateUpdateRequest $lessonCreateUpdateRequest)
    {
        $lesson = $this->repository->createUpdate($lessonCreateUpdateRequest);
        new LessonPageResource($lesson);
        return $lesson->id;
    }

    public function getLesson($local, $id)
    {
        $lesson = Lesson::where('id', $id)->firstOrFail();
        return response()->json(new LessonResource($lesson));
    }

    public function deleteLesson($local, $slug)
    {
        $lesson = Lesson::where('slug', $slug)->firstOrFail();
        $lesson = $lesson->delete();
        return response("OK", 200);
    }

    public function getArticleRefrence(Request $request)
    {
        $lesson = Lesson::where('id', $request->id)->firstOrFail();

        $content = $lesson->content;
        $articleIds = [];

        foreach ($content as $item) {
            if (is_array($item) && isset($item['type']) && $item['type'] === 'reference') {
                $articleIds = $item['content'];
                break;
            }
        }

        $articles = Article::whereIn('id', $articleIds)->get();
        return $articles;
    }
}
