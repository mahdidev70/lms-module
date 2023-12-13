<?php

namespace TechStudio\Lms\app\Http\Controllers;

use TechStudio\lms\app\Models\Skill;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TechStudio\Lms\app\Http\Requests\SkillRequest;
use TechStudio\Lms\app\Http\Resources\SkillResource;
use TechStudio\Lms\app\Repositories\Interfaces\SkillRepositoryInterface;

class SkillController extends Controller
{
    private SkillRepositoryInterface $repository;

    public function __construct(SkillRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getSkillList(Request $request)
    {
        $skillList = $this->repository->list($request);
        return $skillList;
    }

    public function editCreateSkill(SkillRequest $skillRequest)
    {
        $skill = $this->repository->createUpdate($skillRequest);
        return $skill->id;
    }

    public function getCommonList() 
    {
        $skill = $this->repository->getCommonSkill();
        return $skill;
    }
}
