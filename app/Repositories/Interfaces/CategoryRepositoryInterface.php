<?php


namespace App\Repositories\Interfaces;


interface CategoryRepositoryInterface
{
    public function getCategoriesWithCourses();
    public function getCategories();
    public function getAllSkills();
}