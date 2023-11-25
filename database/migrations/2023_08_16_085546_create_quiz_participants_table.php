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
        Schema::create('lms_quiz_participants', function (Blueprint $table) {
            $table->id();
            $table->integer('lesson_id');
            $table->integer('user_id');
            $table->json('selected_choices')->nullable();
            $table->integer('score')->nullable();
            $table->enum('status',['start', 'fail', 'success']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_quiz_participants');
    }
};
