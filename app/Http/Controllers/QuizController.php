<?php

namespace TechStudio\Lms\app\Http\Controllers;

use TechStudio\Lms\app\Models\UserLessonProgress;
use stdClass;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Lms\QuizParticipateRequest;
use App\Http\Resources\Lms\LessonPageResource;
use App\Http\Resources\Lms\QuizListResource;
use App\Http\Resources\Lms\QuizPageResource;
use App\Http\Resources\Lms\QuizResultResource;
use App\Models\Lesson;
use App\Repositories\Interfaces\LessonRepositoryInterface;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use Carbon\Carbon;

class QuizController extends Controller
{
    private LessonRepositoryInterface $lessonRepository;
    private CourseRepositoryInterface $courseRepository;

    public function __construct(
        LessonRepositoryInterface $lessonRepository,
        CourseRepositoryInterface $courseRepository,
    ) {
        $this->lessonRepository = $lessonRepository;
        $this->courseRepository = $courseRepository;
    }

    public function participate(QuizParticipateRequest $request, $quizId)
    {
        $lastResult = $this->lessonRepository->getLastQuizResult($quizId, Auth::user()->id);

        if (
            ($lastResult &&
                $lastResult->status == 'success')
            ||
            ($lastResult && $lastResult->status == 'fail'
                &&  $lastResult->created_at > Carbon::now()->subHours(1))
        ) {
            return response()->json(new QuizResultResource($lastResult));
        }

        $quiz = $this->lessonRepository->getQuizById($quizId);
        $tureAnswers = collect(json_decode($quiz->information)->trueAnswers);
        $diff = $tureAnswers->diffAssoc($request->trueAnswers);
        $falseAnswers = $diff->all();

        $answerNumber = count((array)json_decode($quiz->information)->trueAnswers);
        $score = ($answerNumber - count((array) $falseAnswers)) * 100 / $answerNumber;

        $status = 'fail';
        if ($score >= 80) {
            $status = 'success';
        }

        $quizResult = $this->lessonRepository->storeQuizResult([
            'lesson_id' => $quizId,
            'user_id' => Auth::user()->id,
            'selected_choices' => [
                'version' => $request->version,
                'trueAnswers' => $request->trueAnswers
            ],
            'score' => intval($score),
            'status' => $status
        ]);
        $userProgress = UserLessonProgress::updateOrInsert(
            ['lesson_id' => $quizId, 'user_id' => Auth::user()->id],
            ['progress'=>1]
        );
        return response()->json(new QuizResultResource($quizResult));
    }

    public function quizList($courseSlug)
    {
        $course = $this->courseRepository->getBySlug($courseSlug);

        // $chapters_id = Chapter::where('course_id', $course->id)->pluck('id');

        // $quiz = Lesson::whereIn('chapter_id', $chapters_id)->where('dominant_type', 'exam')->get();

        return response()->json(new QuizPageResource($course));
    }
}
