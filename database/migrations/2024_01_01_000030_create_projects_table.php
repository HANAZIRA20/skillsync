<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('selected_student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('category')->nullable();
            $table->json('required_skills'); // e.g. ["Laravel", "Vue.js", "PostgreSQL"]
            $table->decimal('budget_min', 15, 2);
            $table->decimal('budget_max', 15, 2);
            $table->decimal('agreed_budget', 15, 2)->nullable();
            $table->date('deadline');
            $table->integer('duration_days')->nullable();
            $table->enum('status', [
                'open',
                'waiting_payment',
                'in_progress',
                'in_review',
                'revision',
                'completed',
                'disputed',
                'cancelled'
            ])->default('open');
            $table->text('notes_for_student')->nullable();
            $table->integer('revision_count')->default(0);
            $table->integer('max_revisions')->default(3);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
