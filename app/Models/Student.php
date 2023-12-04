<?php

namespace TechStudio\Lms\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TechStudio\Core\app\Models\UserProfile;

class Student extends Model
{
    use HasFactory;

    protected $table = 'lms_students';

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
