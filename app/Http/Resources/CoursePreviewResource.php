<?php

namespace App\Http\Resources\Lms;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;

class CoursePreviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $studentCount = $this->students()->count();
        $rateCount = $this->students()->where('rate','>=',0)->count();
        $rateSum = $this->students()->where('rate','>=',0)->sum('rate');
        $commentCount = $this->comments()->count();
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'instructor' => new InstructorResource($this->instructor),
            'thumbnail_url' => $this->thumbnail_url,
            'bannerUrl' => $this->banner_url,
            'bannerUrlMobile' => $this->banner_url_mobile,
            'participantsCount' => $studentCount,
            'totalDurationMinutes' => $this->duration,
            'totalDuration' => $this->getTotalDuration(),
            'videosCount' => $this->videos_count,
            'languages' => json_decode($this->languages),
            'ratingsCount' => $rateCount,
            'averageRating' => $rateSum ?? 0 / $rateCount ?? 0,
            'commentCount' => $commentCount,
            'level' => $this->level,
            'certificateEnabled' => $this->certificate_enabled,
            'instructorSupport' => $this->instructor_support,
            'moneyReturnGuarantee' => $this->money_return_guarantee,
            'createAt' => $this->created_at
        ];
    }
}
