<?php

namespace TechStudio\Lms\app\Http\Controllers;

use TechStudio\lms\app\Models\Skill;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TechStudio\Lms\app\Http\Request\SkillRequest;
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
        $query = Skill::withCount('courses');
        
        if ($request->filled('search')) {
            $txt = $request->get('search');
        
            $query->where(function ($q) use ($txt) {
                $q->where('title', 'like', '%' . $txt . '%');
            });
        }

        $skill = $query->paginate(10);

        return $skill;
    }

    public function editCreateSkill(SkillRequest $skillRequest)
    {
        $skill = $this->repository->createUpdate($skillRequest);
        return $skill->id;
    }

    public function getCommonList() 
    {
        $counts = [
            'all' => Skill::all()->count(),
            'active' => Skill::where('status', 1)->count(),
            'hidden' => Skill::where('status', 0)->count(),
        ];

        return $counts;
    }
}
