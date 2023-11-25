<?php

namespace TechStudio\Lms\app\Providers;

use Illuminate\Support\ServiceProvider;
use TechStudio\Lms\app\Repositories\CourseRepository;
use TechStudio\Lms\app\Repositories\ChapterRepository;
use TechStudio\Lms\app\Repositories\CommentRepository;
use TechStudio\Lms\app\Repositories\LessonRepository;
use TechStudio\Lms\app\Repositories\StudentRepository;
use TechStudio\Lms\app\Repositories\UserRepository;
use TechStudio\Lms\app\Repositories\CategoryLmsRepository;
use TechStudio\Lms\app\Repositories\Interfaces\CategoryLmsRepositoryInterface;
use TechStudio\Lms\app\Repositories\Interfaces\ChapterRepositoryInterface;
use TechStudio\Lms\app\Repositories\Interfaces\CommentRepositoryInterface;
use TechStudio\Lms\app\Repositories\Interfaces\CourseRepositoryInterface;
use TechStudio\Lms\app\Repositories\Interfaces\LessonRepositoryInterface;
use TechStudio\Lms\app\Repositories\Interfaces\SkillRepositoryInterface;
use TechStudio\Lms\app\Repositories\Interfaces\StudentRepositoryInterface;
use TechStudio\Lms\app\Repositories\Interfaces\UserRepositoryInterface;
use TechStudio\Lms\app\Repositories\SkillRepository;

class LmsRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ChapterRepositoryInterface::class, ChapterRepository::class);
        $this->app->bind(LessonRepositoryInterface::class, LessonRepository::class);
        $this->app->bind(CourseRepositoryInterface::class, CourseRepository::class);
        $this->app->bind(CommentRepositoryInterface::class, CommentRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(StudentRepositoryInterface::class, StudentRepository::class);
        $this->app->bind(SkillRepositoryInterface::class, SkillRepository::class);
        $this->app->bind(CategoryLmsRepositoryInterface::class, CategoryLmsRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
