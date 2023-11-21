<?php

namespace TechStudio\Lms\app\Http\Controllers;

use TechStudio\Lms\app\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lms\LessonCreateUpdateRequest;
use App\Http\Resources\Lms\LessonPageResource;
use App\Http\Resources\Lms\LessonResource;
use App\Repositories\Interfaces\LessonRepositoryInterface;
use TechStudio\Blog\app\Models\Article;

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

    public function editCreateLesson(LessonCreateUpdateRequest $lessonCreateUpdateRequest)
    {
        $lesson = $this->repository->createUpdate($lessonCreateUpdateRequest);
        new LessonPageResource($lesson);
        return $lesson->id;
    }

    public function getLesson($id)
    {
        $lesson = Lesson::where('id', $id)->firstOrFail();
        return response()->json(new LessonResource($lesson));
    }

    public function deleteLesson($slug) 
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
