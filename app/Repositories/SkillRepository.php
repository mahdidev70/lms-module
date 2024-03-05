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

        $sortOrder= 'desc';
        if (isset($data->sortOrder) && ($data->sortOrder ==  'asc' || $data->sortOrder ==  'desc')) {
            $sortOrder = $data->sortOrder;
        }

        if ($data->has('sortKey')) {
            if ($data->sortKey == 'courseCount') {
                $query->withCount('courses')->orderBy('courses_count', $sortOrder);
            }
        }
        
        $skill = $query->orderBy('id', $sortOrder)->paginate(10);

        return $skill;
    }

    public function createUpdate($data)
    {
        $skill = Skill::updateOrCreate(
            ['id' => $data['id']],
            [
                'title' => $data['title'],
                'slug' => $data['slug'] ? $data['slug'] : SlugGenerator::transform($data['title']),
                'description' => $data['description'],
            ]
        );

        $data = Skill::where('id', $skill->id)->first();

        return $data;
    }

    public function getCommonSkill()
    {
        $counts = [
            'all' => Skill::all()->count(),
            'active' => Skill::where('status', 1)->count(),
            'hidden' => Skill::where('status', 0)->count(),
        ];

        $status = ['active','hidden','deleted'];

        return [
            'counts' => $counts,
            'status' => $status,
        ];
    }

    public function changeStatus($data)
    {
        $validatedData = $data->validate([
            'status' => 'required|in:active,hidden,deleted',
            'ids' => 'required|array',
        ]);

        $skill = Skill::whereIn('id', $validatedData['ids'])->get();

        $ids = collect($validatedData['ids']);

        if ($validatedData['status'] != 'active') {
            if($skill->first()->courses()->exists()) {
                return response()->json([
                    'message' => 'برای تغییر وضعیت این دسته بندی ابتدا زیرمجموعه‌های آن را بردارید'
                ], 409);
            }
        }

        Skill::whereIn('id', $ids)->update(['status' => $validatedData['status']]);

        return [
            'updateSkill' => $ids,
        ];
    }
}
