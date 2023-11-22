<?php

namespace TechStudio\Lms\app\Repositories\Interfaces;

interface StudentRepositoryInterface
{
    public function getUserCourses($userId);
    public function getUserInrolledCourses($userId);
    public function getUserBookmarkedCourses($userId);
    public function getUserProgressCourses($userId);
    public function getUserDoneCourses($userId);
    public function getUserRecentlyVisited();
    public function getNecessaryCourses();
    public function getStudentList($request);
    public function certificatesByStudent($request);
}