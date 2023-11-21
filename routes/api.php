<?php

use TechStudio\Lms\app\Http\Controllers\CourseController;
use TechStudio\Lms\app\Http\Controllers\HomeController;
use TechStudio\Lms\app\Http\Controllers\ChapterController;
use TechStudio\Lms\app\Http\Controllers\LessonController;
use TechStudio\Lms\app\Http\Controllers\SkillController;
use TechStudio\Lms\app\Http\Controllers\QuizController;
use TechStudio\Lms\app\Http\Controllers\StudentController;
use TechStudio\Lms\app\Http\Controllers\UserController;
use TechStudio\Core\app\Http\Controllers\CategoriesController;
use TechStudio\Core\app\Http\Controllers\CommentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;


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


if (!Config::get('flags.academy')) {
    return;
}

Route::prefix('academy')->group(function () {

    // ============ CLIENT SIDE ===============
    
    // Route::get('/home', [HomeController::class, 'index']);
    // Route::get('/course/list', [CourseController::class, 'getAllCourseData']);
    // Route::get('/course/filters', [CourseController::class, 'filters']);
    
    // Route::middleware('login_optional')->prefix('course')->group(function() {
        
    //     Route::get('/{courseSlug}', [CourseController::class, 'getCourseData']);
    //     Route::get('/room/list/{courseSlug}', [CourseController::class, 'getCourseRoom']);
        
    // });

    Route::middleware('login_required')->group(function() {

        // Route::get('/chapter/{chapterSlug}', [ChapterController::class,'show']);
        // Route::get('/lesson/{lessonSlug}', [LessonController::class, 'show']);

        // Route::get('/user/panel', [UserController::class, 'index']);
    
    //     Route::post('/quiz/participate/{quizId}', [QuizController::class, 'participate']);
    
    //     Route::post('/course/rate',  [StudentController::class, 'storeCertificate']);
    //     Route::post('/course/bookmark',  [StudentController::class, 'storeBookmark']);
    
    //     Route::get('/course/quiz/list/{courseSlug}', [QuizController::class, 'quizList']);
    
    //     // ============== PANEL ================
    
    //     Route::prefix('panel')->group(function () {
            
    //         Route::get('/instructor/list', [UserController::class, 'instructors']);
    
    //         Route::get('/course/list', [CourseController::class, 'courseList']);
    
    //         Route::put('course_editor/data', [CourseController::class, 'editCreateCourse']);
    
    //         Route::put('/chapter_editor/data', [ChapterController::class, 'editCreateCahpter']);
    
    //         Route::put('lesson_editor/data', [LessonController::class, 'editCreateLesson']);
    
    //         Route::get('/comment/list', [CommentController::class, 'getCourseCommnetsList']);
    
    //         Route::put('/comment_editor/data', [CommentController::class, 'editCreateCommentCourse']);
    
    //         Route::get('/skill/list', [SkillController::class, 'getSkillList']);
    
    //         Route::put('/skill_editor/data', [SkillController::class, 'editCreateSkill']);
    
    //         Route::get('/category/list', [CategoriesController::class, 'getCourseCategoryList']);
    
    //         Route::put('/category_editor/data', [CategoriesController::class, 'editCreateCategoryCourse']);
    
    //         Route::get('/course_editor/common', [CourseController::class, 'getCommonCreateUpdate']);
    
    //         Route::get('/course/list/common', [CourseController::class, 'getCommonList']);
    
    //         Route::get('/students/list', [StudentController::class, 'getStudentList']);
            
    //         Route::get('/instructor/list/common', [UserController::class, 'getCommonList']);
            
    //         Route::get('/comment/list/common', [CommentController::class, 'getCourseCommonList']);
    
    //         Route::get('/skill/list/common', [SkillController::class, 'getCommonList']);
    
    //         Route::get('/category/list/common', [CategoriesController::class, 'getCourseCategoyCommon']);
    
    //         Route::get('/chapter/list/{id}', [ChapterController::class, 'getChapterLessonList']);
    
            // Route::get('/course/{id}', [CourseController::class, 'getCourse']);

    // =============================== NEW ROUTES ===========================================

            // Route::get('lesson/{id}', [LessonController::class, 'getLesson']);

            // Route::delete('chapter/{slug}', [ChapterController::class, 'deleteChapter']);

            // Route::delete('lesson/{slug}', [LessonController::class, 'deleteLesson']);

            // Route::get('lesson/article/reference', [LessonController::class, 'getArticleRefrence']);

            // Route::put('set_status', [CourseController::class, 'editStatus']);

            // Route::put('comment/status', [CommentController::class, 'updateCommentsStatus']);

            // Route::put('category/status', [CategoriesController::class, 'updateCategoryStatus']);

            // Route::get('view_dashboard', [CourseController::class, 'viewDashboard']);

            // Route::get('certificate/list', [StudentController::class, 'certificateByStudentList']);

            // Route::get('certificate/common', [StudentController::class, 'certificateCommon']);

            // Route::get('student/common', [StudentController::class, 'studentCommonList']);

            // Route::get('comment/excel/export', [CommentController::class, 'exportExcel']);
    
    //     });
    });
    
});