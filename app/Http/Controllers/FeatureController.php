<?php

namespace TechStudio\Lms\app\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use TechStudio\Lms\app\Http\Requests\FeatureRequest;
use TechStudio\Lms\app\Http\Resources\CourseResource;
use TechStudio\Lms\app\Http\Resources\FeatureResource;
use TechStudio\Lms\app\Http\Requests\FeatureDeleteRequest;
use TechStudio\Lms\app\Repositories\Interfaces\CourseRepositoryInterface;

class FeatureController extends Controller
{
    private CourseRepositoryInterface $repository;

    public function __construct(
        CourseRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function getAllFeatures()
    {
        $features = $this->repository->getAllFeatures();
        return FeatureResource::collection($features);
    }

    public function updateOrCreate(FeatureRequest $request)
    {
        $feature = $this->repository->featureUpdateCreate($request);
        return new FeatureResource($feature);
    }

    public function delete(FeatureDeleteRequest $request)
    {
        $this->repository->featureDelete($request);
        return response()->json([
            'message' => ' با موفقیت حذف شد.',
        ], 200);
    }
}
