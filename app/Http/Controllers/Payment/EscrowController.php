<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Payment;
use App\Services\EscrowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EscrowController extends Controller
{
    public function __construct(private EscrowService $escrowService) {}

    public function show(Project $project)
    {
        $client = Auth::user()->client;
        abort_if($project->client_id !== $client->id, 403);

        $payment = $project->payment;
        return view('payment.escrow', compact('project', 'payment'));
    }

    public function deposit(Request $request, Project $project)
    {
        $client = Auth::user()->client;
        abort_if($project->client_id !== $client->id, 403);

        if ($project->payment) {
            return back()->with('error', 'Pembayaran sudah dibuat sebelumnya.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:50000',
        ]);

        $payment = $this->escrowService->createEscrow($project, $request->amount);

        return redirect()->route('payment.escrow', $project->id)
            ->with('success', "Escrow berhasil dibuat! Silakan transfer ke Virtual Account: {$payment->mock_callback_data['va_number']}");
    }

    public function callback(Request $request, Payment $payment)
    {
        $client = Auth::user()->client;
        abort_if($payment->client_id !== $client->id, 403);

        try {
            $this->escrowService->processPaymentCallback($payment);
            return redirect()->route('workroom.show', $payment->project_id)
                ->with('success', '🎉 Pembayaran berhasil! Dana ditahan dalam escrow. Workroom telah dibuka!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function release(Request $request, Payment $payment)
    {
        $client = Auth::user()->client;
        abort_if($payment->client_id !== $client->id, 403);

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:500',
        ]);

        try {
            $this->escrowService->releaseFunds($payment, $request->rating, $request->review);
            return redirect()->route('client.dashboard')
                ->with('success', '✅ Dana berhasil dicairkan ke mahasiswa! Proyek selesai.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function refund(Request $request, Payment $payment)
    {
        $client = Auth::user()->client;
        abort_if($payment->client_id !== $client->id, 403);

        try {
            $this->escrowService->refundPayment($payment, $request->reason ?? 'Refund oleh client');
            return redirect()->route('client.dashboard')
                ->with('success', 'Dana berhasil dikembalikan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
