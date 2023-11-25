<?php

namespace TechStudio\Lms\app\Repositories\Interfaces;

interface CategoryLmsRepositoryInterface
{
    public function getCategoriesWithCourses();
    public function getCategories();
    public function getAllSkills();
}