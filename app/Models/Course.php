<?php

namespace TechStudio\Lms\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use TechStudio\Core\app\Models\Category;
use TechStudio\Core\app\Models\Comment;
use Illuminate\Database\Eloquent\Builder;
use TechStudio\Community\app\Models\ChatRoom;

class Course extends Model
{
    use HasFactory;

    protected $table = 'lms_courses';

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        if (!request()->is(['api/academy/panel/*', '/academy/course/{courseSlug}/'])) {
            static::addGlobalScope('publishedCourse', function (Builder $builder) {
                $builder->where('status', 'published');
            });
        }
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }

    public function instructor()
    {
        return $this->morphTo();
    }

    public function students()
    {
        return $this->hasMany(Student::class,'course_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id')
            ->where('table_type', get_class($this));
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class,'lms_course_skill');
    }

    public function rooms()
    {
        return $this->hasMany(ChatRoom::class);
    }

    public function getTotalDuration() {
        if($this->total_duration < 10080){
            return intval($this->total_duration / 1440) .' روز';
        }elseif($this->total_duration > 10080 && $this->total_duration < 43800){
            return intval($this->total_duration / 10080) .' هفته';
        }else{
            return (int)$this->total_duration / 43800 .' هفته';
        }
    }
}
