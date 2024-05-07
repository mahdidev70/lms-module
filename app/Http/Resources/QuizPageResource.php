<?php

namespace TechStudio\Lms\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;
use TechStudio\Lms\app\Models\Chapter;
use TechStudio\Lms\app\Models\Lesson;
use TechStudio\Lms\app\Models\QuizParticipant;
use TechStudio\Lms\app\Models\Student;

class QuizPageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // $course = $this->courseRepository->getBySlug($courseSlug);
        $chaptersIds = Chapter::where('course_id', $this->id)->pluck('id');
        $quiz = Lesson::whereIn('chapter_id', $chaptersIds)->orderBy('created_at', 'desc')->where('dominant_type', 'exam')->get();
        $quizIds = $quiz->pluck('id')->toArray();
     //   $quizIds = Lesson::whereIn('chapter_id', $chaptersIds)->where('dominant_type', 'exam')->pluck('id');
        $participants = QuizParticipant::whereIn('lesson_id', $quizIds)
        ->where('user_id', Auth('sanctum')->user()->id)->sum('score');

        $average = 0;
        $pass = false;
        $certificate = null;
        $rate = null;

        if($participants > 0){
            $average = $participants / count($quizIds);
        }
        if($average > 80){
            $pass = true;
            $student = Student::where('course_id',$this->id)->where('user_id', Auth('sanctum')->user()->id)->first();
            if($student){
                $certificate = $student->certificate_file;
                $rate = $student->rate;
            }
        }
        return [
            'id' => $this->id,
            'sidebar' => new CourseSidebarResource($this),
            'score' => $average,
            'passed' => $pass,
            'certificate' => 'https://storage.sa-test.techstudio.diginext.ir/static/free-printable-certificate-design-template-cbf0882bc95c38a60fbf0570406cf533_screen.jpg',
            'rate' => $rate,
            'quizzes' => LessonResource::collection($quiz)
        ];
    }
}
