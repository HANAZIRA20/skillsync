<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade')->unique();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->json('messages')->nullable(); // chat history
            $table->json('deliverables')->nullable(); // list file paths
            $table->text('current_deliverable_notes')->nullable();
            $table->enum('status', ['active', 'submitted', 'in_review', 'revision', 'approved', 'disputed'])->default('active');
            $table->integer('progress_percentage')->default(0);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workrooms');
    }
};
