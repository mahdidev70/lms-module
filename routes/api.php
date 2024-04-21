<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use TechStudio\Lms\app\Http\Controllers\HomeController;
use TechStudio\Lms\app\Http\Controllers\QuizController;
use TechStudio\Lms\app\Http\Controllers\UserController;
use TechStudio\Lms\app\Http\Controllers\SkillController;
use TechStudio\Lms\app\Http\Controllers\CourseController;
use TechStudio\Lms\app\Http\Controllers\LessonController;
use TechStudio\Lms\app\Http\Controllers\ChapterController;
use TechStudio\Lms\app\Http\Controllers\FeatureController;
use TechStudio\Lms\app\Http\Controllers\StudentController;
use TechStudio\Core\app\Http\Controllers\CommentController;
use TechStudio\Core\app\Http\Controllers\CategoriesController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::prefix('academy')->group(function () {

    // ============ CLIENT SIDE ===============

    Route::get('/home', [HomeController::class, 'index']); //Done

    Route::get('/course/list', [CourseController::class, 'getAllCourseData']); //Done

    Route::get('/course/filters', [CourseController::class, 'filters']); //Done

    Route::get('/course/{courseSlug}', [CourseController::class, 'getCourseData']); //Done

    Route::middleware('auth:sanctum')->group(function() {

        Route::get('/room/list/{courseSlug}', [CourseController::class, 'getCourseRoom']);

        Route::get('/chapter/{chapterSlug}', [ChapterController::class,'show']); //Done

        Route::get('/lesson/{lessonSlug}', [LessonController::class, 'show']); //Done

        Route::get('/user/panel', [UserController::class, 'index']); //Done

        Route::post('/quiz/participate/{quizId}', [QuizController::class, 'participate']); //Done

        Route::post('/course/rate',  [StudentController::class, 'storeCertificate']); //Done

        Route::post('/course/bookmark',  [StudentController::class, 'storeBookmark']); //Done

        Route::get('/course/quiz/list/{courseSlug}', [QuizController::class, 'quizList']); //Done

        // ============== PANEL ================

        Route::prefix('panel')->group(function () {

            Route::get('/instructor/list', [UserController::class, 'instructors']); //Done

            Route::get('/course/list', [CourseController::class, 'courseList']); //Done

            Route::put('course_editor/data', [CourseController::class, 'editCreateCourse']); //Done

            Route::put('/chapter_editor/data', [ChapterController::class, 'editCreateCahpter']); //Done

            Route::put('lesson_editor/data', [LessonController::class, 'editCreateLesson']); //Done

            Route::get('/comment/list', [CommentController::class, 'getCourseCommnetsList']); //Done

            Route::put('/comment_editor/data', [CommentController::class, 'editCreateCommentCourse']); //Done

            Route::get('/skill/list', [SkillController::class, 'getSkillList']); //Done

            Route::put('/skill_editor/data', [SkillController::class, 'editCreateSkill']); //Done

            Route::get('/category/list', [CategoriesController::class, 'getCourseCategoryList']); //Done

            Route::put('/category_editor/data', [CategoriesController::class, 'editCreateCategoryCourse']); //Done

            Route::get('/course_editor/common', [CourseController::class, 'getCommonCreateUpdate']); //Done

            Route::get('/course/list/common', [CourseController::class, 'getCommonList']); //Done

            Route::get('/students/list', [StudentController::class, 'StudentList']); //Done =sellerBug=

            Route::get('students/export', [StudentController::class, 'StudentListExport']);

            Route::get('/instructor/list/common', [UserController::class, 'getInstructorCommonList']); //Done

            Route::get('/comment/list/common', [CommentController::class, 'getCourseCommonList']); //Done

            Route::get('/skill/list/common', [SkillController::class, 'getCommonList']); //Done

            Route::get('/category/list/common', [CategoriesController::class, 'getCourseCategoyCommon']); //Done =sellerBug=

            Route::get('/chapter/list/{id}', [ChapterController::class, 'getChapterLessonList']); //Done

            Route::get('/course/{id}', [CourseController::class, 'getCourse']); //Done

            Route::get('/feature/list', [FeatureController::class, 'getAllFeatures']);
            Route::put('/feature/editor', [FeatureController::class, 'updateOrCreate']);
            Route::delete('/feature/delete', [FeatureController::class, 'delete']);
    // =============================== NEW ROUTES ===========================================
            Route::get('lesson/{id}', [LessonController::class, 'getLesson']); //Done

            Route::delete('chapter/{slug}', [ChapterController::class, 'deleteChapter']); //Done

            Route::delete('lesson/{slug}', [LessonController::class, 'deleteLesson']); //Done

            Route::get('lesson/article/reference', [LessonController::class, 'getArticleRefrence']); //Done

            Route::put('set_status', [CourseController::class, 'editStatus']); //Done =sellerBug=

            Route::put('comment/status', [CommentController::class, 'updateCommentsStatus']);

            Route::put('category/status', [CategoriesController::class, 'updateCategoryStatus']); 

            Route::get('view_dashboard', [CourseController::class, 'viewDashboard']); 

            Route::get('certificate/list', [StudentController::class, 'certificateByStudentList']); 

            Route::get('certificate/common', [StudentController::class, 'certificateCommon']); 

            Route::get('students/list/common', [StudentController::class, 'studentCommonList']); 

            Route::get('comment/excel/export', [CommentController::class, 'exportExcel']); 

            Route::put('skill/status', [SkillController::class, 'changeSkillStatus']);

            Route::get('course/preview/{id}', [CourseController::class, 'coursePreview']);
            Route::get('course/chapter/preview/{slug}', [ChapterController::class, 'chapterPreview']);
        });
    });
});
