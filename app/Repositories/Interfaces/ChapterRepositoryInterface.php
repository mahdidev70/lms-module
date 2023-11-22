<?php

namespace TechStudio\Lms\app\Repositories\Interfaces;

interface ChapterRepositoryInterface
{
    public function getBySlug($slug);
    public function createUpdate($data);
}