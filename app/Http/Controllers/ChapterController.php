<?php

namespace TechStudio\Lms\app\Http\Controllers;

use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lms\ChapterCreateUpdateRequest;
use App\Http\Resources\Lms\ChapterPageResource;
use App\Http\Resources\Lms\ChapterResource;
use App\Repositories\Interfaces\ChapterRepositoryInterface;

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

        new ChapterPageResource($chapter);

        return $chapter->id;
    }

    public function getChapterLessonList($id)
    {

        $chapter = Chapter::with('lessons')->where('course_id', $id)->paginate(10);

        return $chapter;

    }

}
