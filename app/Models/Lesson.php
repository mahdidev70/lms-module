<?php

namespace TechStudio\Blog\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\LessonService;
use Illuminate\Support\Facades\Auth;

class Lesson extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'content' => 'json',
    ];

    public function chapter() 
    {
        return $this->belongsTo(Chapter::class, 'chapter_id');
    }

    public function updateSummary()
    {
        $this->article->updateSummary();
    }

    public function getAllImageUrls()
    {
        return $this->article->getAllImageUrls();
    }

    public function minutesToRead()
    {
        return $this->article->minutesToRead();
    }

    public function userProgress(){
        return $this->hasMany(UserLessonProgress::class);
    }

    public function userQuizResult(){
        return $this->hasMany(QuizParticipant::class,'lesson_id');
    }
}
