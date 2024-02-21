<?php

namespace TechStudio\Lms\app\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use stdClass;
use App\Http\Controllers\Controller;
use TechStudio\Lms\app\Http\Resources\HomePageResource;
use TechStudio\Lms\app\Repositories\Interfaces\CategoryLmsRepositoryInterface;
use TechStudio\Lms\app\Repositories\Interfaces\CommentRepositoryInterface;

class HomeController extends Controller
{
    private CategoryLmsRepositoryInterface $categoryRepository;
    private CommentRepositoryInterface $commentRepository;

    public function __construct(
        CategoryLmsRepositoryInterface $categoryRepository,
        CommentRepositoryInterface $commentRepository,
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->commentRepository = $commentRepository;
    }

    public function index()
    {
      /*  $data = new stdClass();
        $data->landingBannerUrl = 'first url';
        $data->categories = $this->categoryRepository->getCategoriesWithCourses();
        $data->outPutSqureUpBannerUrl = 'second url';
        $data->outPutSqureDownBannerUrl = 'third url';
        $data->outPutRectangleBannerUrl = 'fourth url';
        $data->comments = $this->commentRepository->getStarComments();*/
        $keyName = 'CommunityHomepageData';
        $minutes = config('cache.long_time')??10080;
        $cachedData = Cache::remember($keyName, $minutes, function () {
            $data = new stdClass();
            $data->landingBannerUrl = 'first url';
            $data->categories = $this->categoryRepository->getCategoriesWithCourses();
            $data->outPutSqureUpBannerUrl = 'second url';
            $data->outPutSqureDownBannerUrl = 'third url';
            $data->outPutRectangleBannerUrl = 'fourth url';
            $data->comments = $this->commentRepository->getStarComments();
            return $data;
        });

        return response()->json(new HomePageResource($cachedData));
    }

}
