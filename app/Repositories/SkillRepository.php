<?php

namespace TechStudio\Lms\app\Repositories;

use TechStudio\Core\app\Helper\SlugGenerator;
use TechStudio\Lms\app\Models\Skill;
use TechStudio\Lms\app\Repositories\Interfaces\SkillRepositoryInterface;

class SkillRepository implements SkillRepositoryInterface
{
    public function list($data) 
    {
        $query = Skill::withCount('courses');
        
        if ($data->filled('search')) {
            $txt = $data->get('search');
        
            $query->where(function ($q) use ($txt) {
                $q->where('title', 'like', '%' . $txt . '%');
            });
        }

        $skill = $query->paginate(10);

        return $skill;
    }

    public function createUpdate($data)
    {
        $skill = Skill::updateOrCreate(
            ['id' => $data['id']],
            [
                'title' => $data['title'],
                'slug' => $data['slug'] ? $data['slug'] : SlugGenerator::transform($data['title']),
                'status' => $data['status'],
                'description' => $data['description'],
            ]
        );

        return $skill;
    }

    public function getCommonSkill() 
    {
        $counts = [
            'all' => Skill::all()->count(),
            'active' => Skill::where('status', 1)->count(),
            'hidden' => Skill::where('status', 0)->count(),
        ];

        return $counts;
    }
}