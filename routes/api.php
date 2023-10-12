<?php




use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Lms\CertificateController;
use App\Http\Controllers\Lms\CourseController;
use App\Http\Controllers\Lms\ChapterController;
use App\Http\Controllers\Lms\HomeController;
use App\Http\Controllers\Lms\LessonController;
use App\Http\Controllers\Lms\QuizController;
use App\Http\Controllers\Lms\SkillController;
use App\Http\Controllers\Lms\StudentController;
use App\Http\Controllers\Lms\UserController;
use App\Models\Student;
use App\Models\UserProfile;
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

// Route::prefix('academy')->group(function () {
//     Route::get('/chapter/{chapterSlug}', [ChapterController::class,'show'])->middleware('login_required');
//     Route::get('/lesson/{lessonSlug}', [LessonController::class, 'show'])->middleware('login_required');
    
//     Route::get('/course/list', [CourseController::class, 'getAllCourseData']);
//     Route::get('/course/filters', [CourseController::class, 'filters']);
//     Route::get('/course/{courseSlug}', [CourseController::class, 'getCourseData'])->middleware('login_optional');
//     Route::get('/course/room/list/{courseSlug}', [CourseController::class, 'getCourseRoom'])->middleware('login_optional');
    
//     Route::get('/home', [HomeController::class, 'index']);
//     Route::get('/user/panel', [UserController::class, 'index'])->middleware('login_required');

//     Route::post('/quiz/participate/{quizId}', [QuizController::class, 'participate'])->middleware('login_required');

//     Route::post('/course/rate',  [StudentController::class, 'storeCertificate'])->middleware('login_required');
//     Route::post('/course/bookmark',  [StudentController::class, 'storeBookmark'])->middleware('login_required');

//     Route::get('/course/quiz/list/{courseSlug}', [QuizController::class, 'quizList'])->middleware('login_required');

//     // ============== PANEL ================

//     Route::prefix('panel')->group(function () {
        
//         Route::get('/instructor/list', [UserController::class, 'instructors']);

//         Route::get('/course/list', [CourseController::class, 'courseList'])->middleware('login_required');

//         Route::put('course_editor/data', [CourseController::class, 'editCreateCourse'])->middleware('login_required');

//         Route::put('/chapter_editor/data', [ChapterController::class, 'editCreateCahpter'])->middleware('login_required');

//         Route::put('lesson_editor/data', [LessonController::class, 'editCreateLesson'])->middleware('login_required');

//         Route::get('/comment/list', [CommentController::class, 'getCourseCommnetsList'])->middleware('login_required');

//         Route::put('/comment_editor/data', [CommentController::class, 'editCreateCommentCourse'])->middleware('login_required');

//         Route::get('/skill/list', [SkillController::class, 'getSkillList'])->middleware('login_required');

//         Route::put('/skill_editor/data', [SkillController::class, 'editCreateSkill'])->middleware('login_required');

//         Route::get('/category/list', [CategoriesController::class, 'getCourseCategoryList'])->middleware('login_required');

//         Route::put('/category_editor/data', [CategoriesController::class, 'editCreateCategoryCourse'])->middleware('login_required');

//         Route::get('/course_editor/common', [CourseController::class, 'getCommonCreateUpdate'])->middleware('login_required');

//         Route::get('/course/list/common', [CourseController::class, 'getCommonList'])->middleware('login_required');

//         Route::get('/students/list', [StudentController::class, 'getStudentList'])->middleware('login_required');
        
//         Route::get('/instructor/list/common', [UserController::class, 'getCommonList'])->middleware('login_required');
        
//         Route::get('/comment/list/common', [CommentController::class, 'getCourseCommonList'])->middleware('login_required');

//         Route::get('/skill/list/common', [SkillController::class, 'getCommonList'])->middleware('login_required');

//         Route::get('/category/list/common', [CategoriesController::class, 'getCourseCategoyCommon'])->middleware('login_required');

//         Route::get('/chapter/list/{id}', [ChapterController::class, 'getChapterLessonList'])->middleware('login_required');

//         Route::get('/course/{id}', [CourseController::class, 'getCourse'])->middleware('login_required');

//     });
// });