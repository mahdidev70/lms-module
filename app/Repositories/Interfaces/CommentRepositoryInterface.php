<?php


namespace App\Repositories\Interfaces;


interface CommentRepositoryInterface
{
    public function getStarComments();
    public function getCourseComments($request);
}