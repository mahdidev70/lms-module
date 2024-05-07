<?php

namespace TechStudio\Lms\app\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use TechStudio\Lms\app\Models\Chapter;
use TechStudio\Lms\app\Http\Resources\ChapterResource;
use TechStudio\Lms\app\Http\Resources\ChapterPageResource;
use TechStudio\Lms\app\Http\Requests\ChapterCreateUpdateRequest;
use TechStudio\Lms\app\Repositories\Interfaces\CourseRepositoryInterface;
use TechStudio\Lms\app\Repositories\Interfaces\ChapterRepositoryInterface;

class ChapterController extends Controller
{
    private ChapterRepositoryInterface $repository;
    private CourseRepositoryInterface $courseRepository;
    public function __construct(
        ChapterRepositoryInterface $repository,
        CourseRepositoryInterface $courseRepository
        )
    {
        $this->repository = $repository;
        $this->courseRepository = $courseRepository;
    }

    public function show($local, $chapterSlug)
    {
        $chapter = $this->repository->getBySlug($chapterSlug);

        return response()->json(new ChapterPageResource($chapter));
    }

    public function editCreateCahpter(ChapterCreateUpdateRequest $chapterCreateUpdateRequest)
    {
        $chapter = $this->repository->createUpdate($chapterCreateUpdateRequest);
        if ($chapter->wasRecentlyCreated) {
            $this->courseRepository->updateCourseEditeTime($chapter->course_id);
        }
        return new ChapterPageResource($chapter);
    }

    public function getChapterLessonList($local, $id)
    {
        $chapters = Chapter::with([
            'lessons' => fn ($query) => $query->orderBy('order', 'asc')
        ])->where('course_id', $id)->paginate(10);
        return ChapterResource::collection($chapters);
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
