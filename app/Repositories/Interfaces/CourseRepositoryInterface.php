<?php

namespace TechStudio\Lms\app\Repositories\Interfaces;

interface CourseRepositoryInterface
{
    public function getBySlug($slug);
    public function getById($id);
    public function all($request);
    public function getAllInstructors();
    public function getInstructors($request);
    public function getTopCourses();
    public function getAllSkills();
    public function createUpdate($data);
    public function incrementField($courseId, $field);
    public function storeView($courseId, $userId);
    public function coursePreview($id);
    public function getAllFeatures();
    public function featureUpdateCreate($request);
    public function featureDelete($request);
}