<?php

namespace TechStudio\Lms\app\Http\Controllers;

use TechStudio\Lms\app\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use TechStudio\Lms\app\Http\Request\ChapterCreateUpdateRequest;
use TechStudio\Lms\app\Http\Resources\ChapterPageResource;
use TechStudio\Lms\app\Repositories\Interfaces\ChapterRepositoryInterface;

class ChapterController extends Controller
{
    private ChapterRepositoryInterface $repository;
    public function __construct(ChapterRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function show($chapterSlug)
    {
        $chapter = $this->repository->getBySlug($chapterSlug);
        return response()->json(new ChapterPageResource($chapter));
    }

    public function editCreateCahpter(ChapterCreateUpdateRequest $chapterCreateUpdateRequest)
    {
        $chapter = $this->repository->createUpdate($chapterCreateUpdateRequest);
        return new ChapterPageResource($chapter);
    }

    public function getChapterLessonList($id)
    {
        $chapter = Chapter::with('lessons')->where('course_id', $id)->paginate(10);
        return ChapterPageResource::collection($chapter);
    }

    public function deleteChapter($slug)
    {
        $chapter = Chapter::where('slug', $slug)->firstOrFail();
        $chapter = $chapter->delete();
        return response("OK", 200);
    }

}
