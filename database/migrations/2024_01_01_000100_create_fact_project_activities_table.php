<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fact_project_activities', function (Blueprint $table) {
            $table->id();
            // Dimension Keys
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('matching_id')->nullable()->constrained('matchings')->nullOnDelete();

            // Activity Type
            $table->string('activity_type'); // project_created, krs_uploaded, ai_matched, payment_held, project_started, revision_requested, project_completed, payment_released, portfolio_updated, etc.
            $table->string('activity_category'); // student, client, ai, payment, project, system

            // Measures
            $table->float('match_score')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->decimal('platform_fee', 15, 2)->nullable();
            $table->integer('revision_count')->nullable();
            $table->integer('duration_days')->nullable();
            $table->float('rating')->nullable();

            // Dimension Attributes (denormalized for OLAP)
            $table->string('project_title')->nullable();
            $table->string('project_category')->nullable();
            $table->string('student_universitas')->nullable();
            $table->string('student_jurusan')->nullable();
            $table->string('client_industry')->nullable();
            $table->string('client_company')->nullable();
            $table->json('skills_involved')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('project_status')->nullable();

            // Time
            $table->date('activity_date');
            $table->integer('activity_month');
            $table->integer('activity_year');
            $table->string('activity_quarter');

            $table->json('extra_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fact_project_activities');
    }
};
