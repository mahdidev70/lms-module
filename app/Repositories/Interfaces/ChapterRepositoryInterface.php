<?php


namespace App\Repositories\Interfaces;


interface ChapterRepositoryInterface
{
    public function getBySlug($slug);
    public function createUpdate($data);
}