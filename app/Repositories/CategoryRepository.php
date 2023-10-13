<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Repositories\Interfaces\CategoryRepositoryInterface;


class CategoryRepository implements CategoryRepositoryInterface
{

    public function getCategoriesWithCourses()
    {
        $course = new Course();
        return $categories = Category::where('table_type', get_class($course))
            ->with('courses')->get();
    }

    public function getCategories()
    {
        $course = new Course();
        return $categories = Category::where('table_type', get_class($course))
            ->get();
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
