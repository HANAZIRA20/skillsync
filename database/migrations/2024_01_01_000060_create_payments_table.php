<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('payment_code')->unique();
            $table->decimal('amount', 15, 2);
            $table->decimal('platform_fee', 15, 2)->default(0); // 10% komisi
            $table->decimal('student_amount', 15, 2)->default(0); // amount - fee
            $table->enum('status', ['pending', 'held', 'released', 'refunded', 'failed'])->default('pending');
            $table->string('payment_method')->default('mock_transfer');
            $table->json('mock_callback_data')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('held_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
