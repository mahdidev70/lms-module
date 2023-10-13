<?php


namespace App\Repositories\Interfaces;


interface LessonRepositoryInterface
{
    public function getBySlug($slug);
    public function getQuizById($id);
    public function storeQuizResult($request);
    public function getLastQuizResult($lessonId,$userId);
    public function createUpdate($data);
}