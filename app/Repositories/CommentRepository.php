<?php

namespace TechStudio\Lms\app\Repositories;

use TechStudio\Core\app\Models\Comment;
use TechStudio\Lms\app\Models\Course;
use TechStudio\Lms\app\Repositories\Interfaces\CommentRepositoryInterface;

class CommentRepository implements CommentRepositoryInterface
{

    public function getStarComments()
    {
        $course = new Course();
        return $comments = Comment::where([
            'commentable_type' => get_class($course),
            'star' => 1
        ])->latest()->with('user')->take(7)->get();
    }

    public function getCourseComments($request)
    {
        $course = new Course();
        return Comment::where(['commentable_type' => get_class($course),]);
    }
}
