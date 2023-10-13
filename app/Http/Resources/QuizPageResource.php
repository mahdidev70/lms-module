<?php

namespace App\Http\Resources\Lms;

use App\Models\Lesson;
use App\Models\Chapter;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\QuizParticipant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;

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
        $quiz = Lesson::whereIn('chapter_id', $chaptersIds)->where('dominant_type', 'exam')->get();
        $quizIds = Lesson::whereIn('chapter_id', $chaptersIds)->where('dominant_type', 'exam')->pluck('id');
        $participants = QuizParticipant::whereIn('lesson_id', $quizIds)
        ->where('user_id', Auth::user()->id)->sum('score');

        $average = 0;
        $pass = false;
        $certificate = null;
        $rate = null;

        if($participants > 0){
            $average = $participants / count($quizIds);
        }
        if($average > 80){
            $pass = true;
            $student = Student::where('course_id',$this->id)->where('user_id', Auth::user()->id)->first();
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
            'certificate' => $certificate,
            'rate' => $rate,
            'quizzes' => LessonResource::collection($quiz)
        ];
    }
}
