<?php

namespace TechStudio\Lms\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizParticipant extends Model
{
    use HasFactory;
    protected $table = 'lms_quiz_participants';

    protected $guarded = ['id'];
    
    protected $casts = [
        'selected_choices' => 'array',
    ];
}
