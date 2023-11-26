<?php

namespace TechStudio\Lms\app\Repositories\Interfaces;

interface SkillRepositoryInterface
{
    public function createUpdate($data);
    public function getCommonSkill();
    public function list($data);
}