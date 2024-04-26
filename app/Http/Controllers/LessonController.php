<?php

namespace TechStudio\Lms\app\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use TechStudio\Lms\app\Models\Lesson;
use Illuminate\Support\Facades\Artisan;
use TechStudio\Blog\app\Models\Article;
use TechStudio\Lms\app\Models\UserLessonProgress;
use TechStudio\Lms\app\Http\Resources\LessonResource;
use TechStudio\Lms\app\Http\Resources\LessonPageResource;
use TechStudio\Lms\app\Http\Requests\LessonOrderUpdateRequest;
use TechStudio\Lms\app\Http\Requests\LessonCreateUpdateRequest;
use TechStudio\Lms\app\Repositories\Interfaces\LessonRepositoryInterface;
use TechStudio\Lms\app\Repositories\Interfaces\ChapterRepositoryInterface;

class LessonController extends Controller
{
    private LessonRepositoryInterface $repository;
    private ChapterRepositoryInterface $chapterRepository;

    public function __construct(
        LessonRepositoryInterface $repository,
        ChapterRepositoryInterface $chapterRepository
    ) {
        $this->repository = $repository;
        $this->chapterRepository = $chapterRepository;
    }

    public function show($local, $lessonSlug)
    {
        $lesson = $this->repository->getBySlug($lessonSlug);
        if ($lesson->dominant_type != 'exam') {
            $this->repository->updateTouchPoint($lesson->id);
        }
        return response()->json(new LessonPageResource($lesson));
    }

    public function editCreateLesson(LessonCreateUpdateRequest $lessonCreateUpdateRequest)
    {
        $lesson = $this->repository->createUpdate($lessonCreateUpdateRequest);
        if ($lesson->wasRecentlyCreated) {
            $chaptersId = $this->chapterRepository->getCourseChaptersId($lesson->chapter->course_id);
            $this->repository->incrementOrders($chaptersId, $lesson->order);
        }
        Artisan::call('lesson-duration:update', ['lessonId' => $lesson->id]);
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
        $chaptersId = $this->chapterRepository->getCourseChaptersId($lesson->chapter->course_id);
        $this->repository->decrementOrders($chaptersId, $lesson->order);
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

    public function updateOrders(LessonOrderUpdateRequest $request)
    {
        $result = $this->repository->updateOrders($request['lessons']);
        if ($result) {
            return response()->json([
                'message' => 'تغییرات با موفقیت ثبت شد.'
            ], 200);
        }
    }
}
