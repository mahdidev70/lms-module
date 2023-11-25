<?php

namespace TechStudio\Lms\app\Repositories;

use TechStudio\Core\app\Helper\SlugGenerator;
use TechStudio\Lms\app\Models\Skill;
use TechStudio\Lms\app\Repositories\Interfaces\SkillRepositoryInterface;

class SkillRepository implements SkillRepositoryInterface
{

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

}