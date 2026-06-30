<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('skill_name');
            $table->string('category')->nullable(); // e.g. "Programming", "Design", "Database"
            $table->float('confidence_score')->default(0); // 0.0 - 1.0
            $table->string('source')->default('ai_parsed'); // ai_parsed | manual | verified
            $table->json('evidence')->nullable(); // mata kuliah yang mendukung skill ini
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_skills');
    }
};
