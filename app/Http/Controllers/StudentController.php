<?php

namespace TechStudio\Lms\app\Http\Controllers;

use TechStudio\Lms\app\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Lms\StudentRequest;
use App\Http\Requests\Lms\BookmarkRequest;
use App\Http\Resources\Lms\CourseResource;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\Interfaces\StudentRepositoryInterface;

class StudentController extends Controller
{
    private CourseRepositoryInterface $courseRepository;
    private StudentRepositoryInterface $studentRepository;

    public function __construct(
        CourseRepositoryInterface $courseRepository,
        StudentRepositoryInterface $studentRepository,
    ) {
        $this->courseRepository = $courseRepository;
        $this->studentRepository = $studentRepository;
    }

    public function storeCertificate(StudentRequest $studentRequest)
    {
        $userId = Auth::user()->id;

        $cer = Student::where('user_id', $userId)->first();

        if ($cer) {
            $cer->course_id = $studentRequest->courseId;
            $cer->rate = $studentRequest->rate;
            $cer->comment = $studentRequest->comment;
            $cer->save();
        } else {
            $cer = new Student();
            $cer->user_id = $userId;
            $cer->course_id = $studentRequest->courseId;
            $cer->rate = $studentRequest->rate;
            $cer->comment = $studentRequest->comment;
            $cer->save();
        }

        return ['success' => true];
    }

    public function storeBookmark(BookmarkRequest $request)
    {
        Student::updateOrInsert(
            ['user_id' => Auth::user()->id, 'course_id' => $request->courseId],
            ['bookmark' => $request->bookmark]
        );
        $course = $this->courseRepository->getById($request->courseId);
        return response()->json(new CourseResource($course));
    }

    public function StudentList(Request $request) 
    {
        $students = $this->studentRepository->getStudentList($request);
        return new StudentsResource($students);
    }

    public function certificateByStudentList(Request $request) 
    {
        $certificates = $this->studentRepository->certificatesByStudent($request);
        return new CertificatesResource($certificates);
    }

    public function certificateCommon() 
    {
        $all = Student::whereNotNull('certificate_file')->count();
        return [
            'counts' => [
                'all' => $all,
            ]
        ];
    }

    public function studentCommonList() 
    {
        $all = Student::distinct()->count('user_id');
        return [
            'counts' => [
                'all' => $all,
            ]
        ];
    }
}
