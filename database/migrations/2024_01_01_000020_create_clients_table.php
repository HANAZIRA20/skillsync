<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company_name')->nullable();
            $table->string('industry')->nullable();
            $table->text('company_description')->nullable();
            $table->string('website')->nullable();
            $table->string('company_size')->nullable(); // e.g. "1-10", "11-50"
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->integer('total_projects_posted')->default(0);
            $table->integer('total_projects_completed')->default(0);
            $table->decimal('total_spent', 15, 2)->default(0);
            $table->float('average_rating_given')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
