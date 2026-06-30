<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->json('skills_used');
            $table->json('deliverable_files')->nullable();
            $table->integer('rating')->nullable(); // 1-5 dari client
            $table->text('client_review')->nullable();
            $table->decimal('earned_amount', 15, 2)->default(0);
            $table->boolean('is_verified')->default(true); // auto verified setelah selesai
            $table->boolean('is_public')->default(true);
            $table->string('client_company')->nullable();
            $table->string('thumbnail')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
