<?php

namespace TechStudio\Lms\app\Repositories\Interfaces;

interface CategoryModuleRepositoryInterface
{
    public function getCategoriesWithCourses();
    public function getCategories();
    public function getAllSkills();
}