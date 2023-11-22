<?php

namespace TechStudio\Lms\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    use HasFactory;

    protected $table = 'lms_skills';

    protected $guarded = ['id'];

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class,'course_skill');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'course_skill')->withTimestamps();
    }

    
}
