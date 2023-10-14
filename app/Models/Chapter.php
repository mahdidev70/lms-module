<?php

namespace TechStudio\Blog\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course;

class Chapter extends Model
{
    use HasFactory;

    protected $table = 'lms_chapters';

    protected $guarded = ['id'];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }
}
