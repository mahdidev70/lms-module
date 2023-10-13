<?php


namespace App\Repositories\Interfaces;


interface CourseRepositoryInterface
{
    public function getBySlug($slug);
    public function getById($id);
    public function all($request);
    public function getAllInstructors();
    public function getInstructors();
    public function getTopCourses();
    public function getAllSkills();
    public function createUpdate($data);
    public function incrementField($courseId, $field);
    public function storeView($courseId, $userId);
}