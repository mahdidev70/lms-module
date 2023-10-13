<?php

namespace App\Repositories;

use App\Models\Course;
use App\Models\Category;
use App\Models\Comment;
use App\Repositories\Interfaces\CommentRepositoryInterface;


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
        return Comment::where([
            'commentable_type' => get_class($course),

        ]);
    }
}
