<?php

namespace TechStudio\Lms\app\Repositories;

use TechStudio\Core\app\Models\Category;
use TechStudio\Lms\app\Models\Course;
use TechStudio\Lms\app\Repositories\Interfaces\CategoryModuleRepositoryInterface;

class CategoryRepository implements CategoryModuleRepositoryInterface
{
    public function getCategoriesWithCourses()
    {
        $course = new Course();
        return Category::where('table_type', get_class($course))->with('courses')->get();
    }

    public function getCategories()
    {
        $course = new Course();
        return Category::where('table_type', get_class($course))->get();
    }

    public function getAllSkills(){
        $skillsArray = [];
        $course = new Course();
        $skills = Category::where('table_type', get_class($course))
        ->pluck('skills');
        foreach($skills as $skill){
            foreach(json_decode($skill) as $index){
                array_push($skillsArray,$index);
            }
        }
        return array_unique($skillsArray);
    }
}
