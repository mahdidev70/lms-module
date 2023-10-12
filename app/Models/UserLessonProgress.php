<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLessonProgress extends Model
{
    use HasFactory;

    protected $table = 'user_lesson_progress';

    protected $guarded = ['id'];

    public function lessons()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }
}
