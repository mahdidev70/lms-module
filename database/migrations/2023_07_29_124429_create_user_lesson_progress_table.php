<?php

namespace TechStudio\Lms\database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lms_user_lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id');
            $table->string('user_id');
            $table->float ('progress');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_user_lesson_progress');
    }
};
