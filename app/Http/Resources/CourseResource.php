<?php

namespace TechStudio\Lms\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;
use TechStudio\Lms\app\Http\Resources\SkillResource;
use TechStudio\Lms\app\Models\Student;

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
        if (Auth::user()) {
            $student = Student::where('course_id', $this->id)
                ->where('user_id', Auth::user()->id)->first();
        }
        $studentCount = $this->students()->count();
        
        $rateCount = $rateSum = $average = 0;
        
        $rateCountResult = $this->students()->where('rate', '>', 0)->count();
        $rateSumResult = $this->students()->where('rate', '>', 0)->sum('rate');
        if($rateCountResult){
            $rateCount = $rateCountResult;
            $average = (int)$rateSumResult / (int)$rateCountResult;
        }
        
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'instructor' => new InstructorResource($this->instructor),
            'instructorCourses' => CoursePreviewResource::collection($this->instructor->courses),
            'bannerUrl' => $this->banner_url,
            'bannerUrlMobile' => $this->banner_url_mobile,
            'currentUserBookmarked' => isset($student->bookmark) ? $student->bookmark : null,
            'currentUserEnroled' => (Auth::user() && isset($student->in_roll)) ? 1 : 0,
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
            'level' => $this->level,
            'certificateEnabled' => $this->certificate_enabled,
            'instructorSupport' => $this->instructor_support,
            'moneyReturnGuarantee' => $this->money_return_guarantee,
            'chapters' => ChapterResource::collection($this->chapters),
            'certificate' => [],
            'comments' => CommentResource::collection($this->comments),
            'skills' => SkillResource::collection($this->skills),
            'faq' => json_decode($this->FAQ),
            'features' => json_decode($this->features),
        ];
    }
}
