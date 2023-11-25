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
        Schema::create('lms_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('course_id');
            $table->enum('in_roll', ['progress', 'done'])->nullable();
            $table->boolean('bookmark')->nullable();
            $table->integer('rate')->nullable();
            $table->string('certificate_file')->nullable();
            $table->text('comment')->nullabel();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_students');
    }
};
