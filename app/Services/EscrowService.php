<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Payment;
use App\Models\FactProjectActivity;
use Illuminate\Support\Str;

class EscrowService
{
    const PLATFORM_FEE_PERCENT = 10; // 10% platform fee

    /**
     * Create escrow payment (Pending status)
     */
    public function createEscrow(Project $project, float $amount): Payment
    {
        $platformFee = $amount * (self::PLATFORM_FEE_PERCENT / 100);
        $studentAmount = $amount - $platformFee;

        $payment = Payment::create([
            'project_id' => $project->id,
            'client_id' => $project->client_id,
            'student_id' => $project->selected_student_id,
            'payment_code' => 'ESC-' . strtoupper(Str::random(8)),
            'amount' => $amount,
            'platform_fee' => $platformFee,
            'student_amount' => $studentAmount,
            'status' => 'pending',
            'payment_method' => 'mock_transfer',
            'mock_callback_data' => [
                'bank' => 'Bank Virtual SkillSync',
                'va_number' => '8888' . rand(100000000, 999999999),
                'expired_at' => now()->addHours(24)->toDateTimeString(),
            ],
        ]);

        // Update project status
        $project->update([
            'status' => 'waiting_payment',
            'agreed_budget' => $amount,
        ]);

        FactProjectActivity::record([
            'project_id' => $project->id,
            'client_id' => $project->client_id,
            'student_id' => $project->selected_student_id,
            'payment_id' => $payment->id,
            'activity_type' => 'payment_created',
            'activity_category' => 'payment',
            'amount' => $amount,
            'platform_fee' => $platformFee,
            'project_title' => $project->title,
            'payment_status' => 'pending',
            'project_status' => 'waiting_payment',
        ]);

        return $payment;
    }

    /**
     * Mock payment callback - Pending → Held
     * Simulates payment gateway callback after user pays
     */
    public function processPaymentCallback(Payment $payment): Payment
    {
        if ($payment->status !== 'pending') {
            throw new \Exception('Payment sudah diproses sebelumnya.');
        }

        $payment->update([
            'status' => 'held',
            'paid_at' => now(),
            'held_at' => now(),
            'mock_callback_data' => array_merge($payment->mock_callback_data ?? [], [
                'callback_received_at' => now()->toDateTimeString(),
                'transaction_id' => 'TXN-' . strtoupper(\Illuminate\Support\Str::random(12)),
                'status_message' => 'Pembayaran berhasil diterima. Dana ditahan dalam escrow.',
            ]),
        ]);

        // Update project status to in_progress
        $payment->project->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        // Create workroom
        if (!$payment->project->workroom) {
            \App\Models\Workroom::create([
                'project_id' => $payment->project_id,
                'student_id' => $payment->student_id,
                'client_id' => $payment->client_id,
                'messages' => [],
                'deliverables' => [],
                'status' => 'active',
                'progress_percentage' => 0,
            ]);
        }

        FactProjectActivity::record([
            'project_id' => $payment->project_id,
            'client_id' => $payment->client_id,
            'student_id' => $payment->student_id,
            'payment_id' => $payment->id,
            'activity_type' => 'payment_held',
            'activity_category' => 'payment',
            'amount' => $payment->amount,
            'platform_fee' => $payment->platform_fee,
            'project_title' => $payment->project->title,
            'payment_status' => 'held',
            'project_status' => 'in_progress',
        ]);

        return $payment->fresh();
    }

    /**
     * Release funds to student (Held → Released)
     */
    public function releaseFunds(Payment $payment, int $rating = null, string $review = null): Payment
    {
        if ($payment->status !== 'held') {
            throw new \Exception('Dana hanya bisa dicairkan dari status Held.');
        }

        $payment->update([
            'status' => 'released',
            'released_at' => now(),
        ]);

        // Update project status
        $payment->project->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Update student earnings
        $student = $payment->student;
        $student->increment('total_earnings', $payment->student_amount);
        $student->increment('total_projects');

        // Update average rating
        if ($rating) {
            $newRating = $student->total_projects > 1
                ? (($student->average_rating * ($student->total_projects - 1)) + $rating) / $student->total_projects
                : $rating;
            $student->update(['average_rating' => round($newRating, 2)]);
        }

        // Update client stats
        $client = $payment->client;
        $client->increment('total_projects_completed');
        $client->increment('total_spent', $payment->amount);

        // Auto-create portfolio entry
        app(PortfolioService::class)->createPortfolioEntry($payment->project, $rating, $review);

        FactProjectActivity::record([
            'project_id' => $payment->project_id,
            'client_id' => $payment->client_id,
            'student_id' => $payment->student_id,
            'payment_id' => $payment->id,
            'activity_type' => 'payment_released',
            'activity_category' => 'payment',
            'amount' => $payment->amount,
            'platform_fee' => $payment->platform_fee,
            'rating' => $rating,
            'project_title' => $payment->project->title,
            'payment_status' => 'released',
            'project_status' => 'completed',
        ]);

        return $payment->fresh();
    }

    /**
     * Refund payment (Held → Refunded) - for disputed projects
     */
    public function refundPayment(Payment $payment, string $reason = ''): Payment
    {
        if (!in_array($payment->status, ['held', 'pending'])) {
            throw new \Exception('Dana hanya bisa direfund dari status Held atau Pending.');
        }

        $payment->update([
            'status' => 'refunded',
            'refunded_at' => now(),
            'notes' => $reason,
        ]);

        $payment->project->update(['status' => 'cancelled']);

        FactProjectActivity::record([
            'project_id' => $payment->project_id,
            'client_id' => $payment->client_id,
            'student_id' => $payment->student_id,
            'payment_id' => $payment->id,
            'activity_type' => 'payment_refunded',
            'activity_category' => 'payment',
            'amount' => $payment->amount,
            'payment_status' => 'refunded',
            'project_status' => 'cancelled',
        ]);

        return $payment->fresh();
    }
}
