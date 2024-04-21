<?php

namespace TechStudio\Lms\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseFeature extends Model
{
    use HasFactory;
    protected $table = 'lms_course_features';
    protected $guarded = ['id'];

}
