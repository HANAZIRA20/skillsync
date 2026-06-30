<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nim')->unique()->nullable();
            $table->string('universitas')->nullable();
            $table->string('jurusan')->nullable();
            $table->integer('semester')->nullable();
            $table->float('ipk')->nullable();
            $table->string('krs_file_path')->nullable();
            $table->json('skill_profile')->nullable(); // hasil AI parsing
            $table->json('available_schedule')->nullable(); // jadwal kosong
            $table->text('bio')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('github_url')->nullable();
            $table->float('average_rating')->default(0);
            $table->integer('total_projects')->default(0);
            $table->decimal('total_earnings', 15, 2)->default(0);
            $table->enum('krs_status', ['not_uploaded', 'uploaded', 'parsed'])->default('not_uploaded');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
