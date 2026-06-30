<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matchings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->float('match_score')->default(0); // 0-100
            $table->json('matched_skills')->nullable(); // skills yang cocok
            $table->json('missing_skills')->nullable(); // skills yang kurang
            $table->json('ai_recommendation')->nullable(); // AI reasoning
            $table->integer('rank')->nullable(); // ranking dalam project
            $table->enum('status', ['pending', 'selected', 'rejected', 'withdrawn'])->default('pending');
            $table->timestamp('selected_at')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matchings');
    }
};
