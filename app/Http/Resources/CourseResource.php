<?php

namespace TechStudio\Lms\app\Http\Resources;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TechStudio\Lms\app\Models\Lesson;
use TechStudio\Lms\app\Models\Chapter;
use TechStudio\Lms\app\Models\Student;
use Illuminate\Http\Resources\Json\JsonResource;
use TechStudio\Lms\app\Models\UserLessonProgress;
use TechStudio\Lms\app\Http\Resources\SkillResource;
use TechStudio\Lms\app\Http\Resources\CoursePreviewResource;
use TechStudio\Lms\app\Services\Calculator;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $student = null;
        if (Auth('sanctum')->user()) {
            $student = Student::where('course_id', $this->id)
                ->where('user_id', Auth('sanctum')->user()->id)->first();
        }
        $studentCount = $this->students()->count();

        $rateCount = $rateSum = $average = 0;

        $rateCountResult = $this->students()->where('rate', '>', 0)->count();
        $rateSumResult = $this->students()->where('rate', '>', 0)->sum('rate');
        if ($rateCountResult) {
            $rateCount = $rateCountResult;
            $average = (int)$rateSumResult / (int)$rateCountResult;
        }

        $calculatorResult = Calculator::courseProgress($this->id);
        $HasDonePrerequisites = 1;
        if (Auth('sanctum')->user()) {
            foreach ($this->prerequisite() as $preRequisit) {
                $student = Student::where('course_id', $preRequisit->id)
                    ->where('user_id', Auth('sanctum')->user()->id)
                    ->where('in_roll', 'done')->first();

                if (!$student) {
                    $HasDonePrerequisites = 0;
                }
            }
        }else{
            $HasDonePrerequisites = 0;
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'instructor' => new InstructorResource($this->instructor),
            'instructorCourses' => CoursePreviewResource::collection($this->instructor->courses) ?? [],
            'bannerUrl' => $this->banner_url,
            'bannerUrlMobile' => $this->banner_url_mobile,
            'currentUserBookmarked' => isset($student->bookmark) ? $student->bookmark : null,
            'currentUserEnroled' => (Auth('sanctum')->user() && isset($student->in_roll)) ? 1 : 0,
            'participantsCount' => $studentCount,
            'category' => new CategoryResource($this->category),
            'totalDurationMinutes' => $this->duration,
            'totalDuration' => $this->getTotalDuration(),
            'videoCount' => $this->videos_count,
            'examCount' => $this->exams_count,
            'languages' => json_decode($this->languages),
            'learningPoints' => json_decode($this->learning_points),
            'supportItems' => json_decode($this->support_items),
            'ratingsCount' => $rateCount,
            'averageRating' => number_format((float)$average, 1, '.', ''),
            'touchPoint' => [
                'lessonSlug' => $calculatorResult['touchPointLesson']->slug ?? null,
                'chapterSlug' => $calculatorResult['touchPointLesson']->chapter->slug ?? null
            ],
            'level' => $this->level,
            'certificateEnabled' => $this->certificate_enabled,
            'instructorSupport' => $this->instructor_support,
            'moneyReturnGuarantee' => $this->money_return_guarantee,
            'chapters' => ChapterResource::collection($this->chapters),
            'certificate' => [],
            'comments' => CommentResource::collection($this->comments),
            'commentCount' => $this->comments->count(),
            'skills' => (isset($this->shortData) && $this->shortData == true) ? $this->skills->pluck('id')
                : SkillResource::collection($this->skills),
            'faq' => json_decode($this->faq),
            'features' => json_decode($this->features),
            'lessonsCount' => $calculatorResult['lessonsCount'] ?? null,
            'passedCount' => $calculatorResult['passedCount'] ?? null,
            'passedPercentage' => $calculatorResult['passedPercentage'] ?? null,
            'completedDate' => $calculatorResult['completedDate'],
            'HasDonePrerequisites' => (bool) $HasDonePrerequisites,
            'prerequisites' => CoursePreviewResource::collection($this->prerequisite())
        ];
    }
}
