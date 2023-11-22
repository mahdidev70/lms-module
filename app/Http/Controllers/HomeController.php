<?php

namespace TechStudio\Lms\app\Http\Controllers;

use stdClass;
use App\Http\Controllers\Controller;
use TechStudio\Lms\app\Http\Resources\HomePageResource;
use TechStudio\Lms\app\Repositories\Interfaces\CategoryModuleRepositoryInterface;
use TechStudio\Lms\app\Repositories\Interfaces\CommentRepositoryInterface;

class HomeController extends Controller
{
    private CategoryModuleRepositoryInterface $categoryRepository;
    private CommentRepositoryInterface $commentRepository;

    public function __construct(
        CategoryModuleRepositoryInterface $categoryRepository,
        CommentRepositoryInterface $commentRepository,
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->commentRepository = $commentRepository;
    }

    public function index()
    {
        $data = new stdClass();
        $data->landingBannerUrl = 'first url';
        $data->categories = $this->categoryRepository->getCategoriesWithCourses();
        $data->outPutSqureUpBannerUrl = 'second url';
        $data->outPutSqureDownBannerUrl = 'third url';
        $data->outPutRectangleBannerUrl = 'fourth url';
        $data->comments = $this->commentRepository->getStarComments();

        return response()->json(new HomePageResource($data));
    }

}
