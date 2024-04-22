<?php

namespace TechStudio\Lms\app\Repositories\Interfaces;

interface ChapterRepositoryInterface
{
    public function preview($slug);
    public function getBySlug($slug);
    public function createUpdate($data);
    public function getCourseChaptersId($request);
}
