<?php

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
        Schema::create('lms_courses', function (Blueprint $table) {

            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->string('instructor_type')->nullable();
            $table->integer('instructor_id')->nullable();
            $table->foreignId('category_id');
            $table->text('description');
            $table->integer('exam_weight')->nullable();
            $table->enum('status', ['published', 'draft', 'hidden', 'deleted'])->default('draft');
            $table->json('faq')->nullable();
            $table->integer('total_duration')->nullable();
            $table->integer('duration')->nullable();
            $table->integer('videos_count')->nullable();
            $table->integer('exams_count')->nullable();
            $table->integer('view_count')->nullable()->default(0);
            $table->string('features')->nullable();
            $table->string('languages')->nullable();
            $table->json('learning_points')->nullable();
            $table->json('support_items')->nullable();
            $table->enum('level', ['beginner','intermediate','advance'])->nullable();
            $table->boolean('necessary')->nullable()->default(0);
            $table->text('thumbnail_url')->nullable();
            $table->text('banner_url')->nullable();
            $table->text('banner_url_mobile')->nullable();
            $table->boolean('certificate_enabled')->nullable();
            $table->boolean('instructor_support')->nullable();
            $table->boolean('money_return_guarantee')->nullable();
            $table->string('publication_date')->nullable();
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_courses');
    }
};
