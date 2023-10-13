<?php

namespace App\Repositories;

use App\Helper\SlugGenerator;
use App\Models\Skill;
use App\Repositories\Interfaces\SkillRepositoryInterface;


class SkillRepository implements SkillRepositoryInterface
{

    public function createUpdate($data)
    {
        $skill = Skill::updateOrCreate(
            ['id' => $data['id']],
            [
                'title' => $data['title'],
                'slug' => SlugGenerator::transform($data['title']),
                'description' => $data['description'],
            ]
        );

        return $skill;
    }

}