<?php

namespace TechStudio\Lms\app\Repositories\Interfaces;

interface LessonRepositoryInterface
{
    public function getBySlug($slug);
    public function getQuizById($id);
    public function createUpdate($data);
    public function storeQuizResult($request);
    public function getLastQuizResult($lessonId,$userId);
    public function incrementOrders($chaptersId, $order);
    public function decrementOrders($chaptersId, $order);
    public function updateOrders($request);
    public function updateTouchPoint($request);
}