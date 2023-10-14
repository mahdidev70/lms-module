<?php

namespace TechStudio\Blog\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function userProfile()
    {
        return $this->belongsTo(UserProfile::class, 'user_id');
    }
}
