<?php

namespace TechStudio\Lms\app\Http\Controllers;

use TechStudio\Lms\app\Models\UserLessonProgress;
use stdClass;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use TechStudio\Lms\app\Http\Requests\QuizParticipateRequest;
use TechStudio\Lms\app\Http\Resources\QuizPageResource;
use TechStudio\Lms\app\Http\Resources\QuizResultResource;
use TechStudio\Lms\app\Repositories\Interfaces\CourseRepositoryInterface;
use TechStudio\Lms\app\Repositories\Interfaces\LessonRepositoryInterface;

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

    public function participate($local, QuizParticipateRequest $request, $quizId)
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
        $tureAnswers = collect(json_decode($quiz->information));
        $flatTrueAnswers = new stdClass();
        foreach($tureAnswers as $answers){
            foreach($answers as $key => $value)
            $flatTrueAnswers->$key = $value;
        }

        $tureAnswers = collect($flatTrueAnswers);
        $userAnswers = new stdClass();
        $request->trueAnswers;

        foreach($request->trueAnswers as $answers){
            foreach($answers as $key => $value)
            $userAnswers->$key = $value;
        }

        $diff = $tureAnswers->diffAssoc(collect($userAnswers));
        $falseAnswers = $diff->all();
        $answerNumber = count((array)json_decode($quiz->information));
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

    public function quizList($local, $courseSlug)
    {
        $course = $this->courseRepository->getBySlug($courseSlug);
        return response()->json(new QuizPageResource($course));
    }
}
