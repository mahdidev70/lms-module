<?php

namespace TechStudio\Lms\app\Repositories\Interfaces;

interface CommentRepositoryInterface
{
    public function getStarComments();
    public function getCourseComments($request);
}