<?php

namespace TechStudio\Lms\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLessonProgress extends Model
{
    use HasFactory;

    protected $table = 'lms_user_lesson_progress';

    protected $guarded = ['id'];

    public function lessons()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }
}
