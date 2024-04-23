<?php

namespace TechStudio\Lms\app\Http\Controllers;

use TechStudio\Lms\app\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use TechStudio\Lms\app\Http\Requests\ChapterCreateUpdateRequest;
use TechStudio\Lms\app\Http\Resources\ChapterPageResource;
use TechStudio\Lms\app\Repositories\Interfaces\ChapterRepositoryInterface;

class ChapterController extends Controller
{
    private ChapterRepositoryInterface $repository;
    public function __construct(ChapterRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function show($local, $chapterSlug)
    {
        $chapter = $this->repository->getBySlug($chapterSlug);

        return response()->json(new ChapterPageResource($chapter));
    }

    public function editCreateCahpter(ChapterCreateUpdateRequest $chapterCreateUpdateRequest)
    {
        $chapter = $this->repository->createUpdate($chapterCreateUpdateRequest);
        return new ChapterPageResource($chapter);
    }

    public function getChapterLessonList($local, $id)
    {
        return Chapter::with([
            'lessons' => fn ($query) => $query->orderBy('order', 'asc')
        ])->where('course_id', $id)->paginate(10);
    }

    public function deleteChapter($local, $slug)
    {
        $chapter = Chapter::where('slug', $slug)->firstOrFail();
        $chapter = $chapter->delete();
        return response("OK", 200);
    }


    public function chapterPreview($locale, $slug)
    {
        $chapter = $this->repository->preview($slug);
        return response()->json(new ChapterPageResource($chapter));
    }
}
