@extends('layouts.app')

@section('title', 'Escrow Pembayaran')
@section('page-title', 'Escrow Pembayaran')
@section('page-subtitle', 'Sistem pembayaran aman dengan escrow')

@section('content')
<div class="max-w-2xl space-y-6 animate-fade-in">

    {{-- Back --}}
    <a href="{{ route('client.project-detail', $project->id) }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-300 transition-colors">
        <i class="fas fa-arrow-left text-xs"></i> Kembali ke Detail Proyek
    </a>

    {{-- Project Summary --}}
    <div class="card p-5">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-primary-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-briefcase text-primary-400"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-white">{{ $project->title }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ $project->category }} · Mahasiswa: {{ $project->selectedStudent?->user->name ?? '—' }}</p>
            </div>
            <span class="badge badge-progress ml-auto">{{ ucfirst($project->status) }}</span>
        </div>
    </div>

    {{-- Escrow Status --}}
    @if($payment)
    <div class="card p-6">
        <h2 class="text-base font-bold text-white mb-5 flex items-center gap-2">
            <i class="fas fa-shield-alt text-accent-400"></i> Status Escrow
        </h2>

        @php
            $payBadge = [
                'pending' => ['class' => 'badge-waiting', 'label' => 'Menunggu Transfer', 'icon' => 'fa-clock'],
                'held' => ['class' => 'badge-progress', 'label' => 'Dana Ditahan Aman', 'icon' => 'fa-lock'],
                'released' => ['class' => 'badge-completed', 'label' => 'Dana Dicairkan', 'icon' => 'fa-check-circle'],
                'refunded' => ['class' => 'badge-revision', 'label' => 'Dana Dikembalikan', 'icon' => 'fa-undo'],
            ][$payment->status] ?? ['class' => 'badge-open', 'label' => $payment->status, 'icon' => 'fa-circle'];
        @endphp

        <div class="flex items-center gap-4 p-4 bg-white/3 rounded-xl border border-white/5 mb-5">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0
                {{ $payment->status === 'held' ? 'bg-accent-500/20' : ($payment->status === 'released' ? 'bg-green-500/20' : 'bg-yellow-500/20') }}">
                <i class="fas {{ $payBadge['icon'] }} text-xl
                    {{ $payment->status === 'held' ? 'text-accent-400' : ($payment->status === 'released' ? 'text-green-400' : 'text-yellow-400') }}"></i>
            </div>
            <div>
                <p class="text-base font-bold text-white">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                <span class="badge {{ $payBadge['class'] }}">{{ $payBadge['label'] }}</span>
            </div>
        </div>

        <div class="space-y-3 mb-5">
            <div class="flex justify-between py-2 border-b border-white/5">
                <span class="text-sm text-gray-400">Total Pembayaran</span>
                <span class="text-sm font-bold text-white">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-white/5">
                <span class="text-sm text-gray-400">Platform Fee (10%)</span>
                <span class="text-sm text-gray-300">Rp {{ number_format($payment->platform_fee, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-white/5">
                <span class="text-sm text-gray-400">Diterima Mahasiswa</span>
                <span class="text-sm font-bold text-accent-400">Rp {{ number_format($payment->amount - $payment->platform_fee, 0, ',', '.') }}</span>
            </div>
            @if($payment->mock_callback_data && $payment->status === 'pending')
            <div class="flex justify-between py-2 border-b border-white/5">
                <span class="text-sm text-gray-400">Nomor Virtual Account</span>
                <span class="text-sm font-mono font-bold text-yellow-300">{{ $payment->mock_callback_data['va_number'] ?? '—' }}</span>
            </div>
            @endif
        </div>

        {{-- Actions based on status --}}
        @if($payment->status === 'pending')
        <div class="p-4 bg-yellow-900/20 border border-yellow-500/30 rounded-xl mb-4">
            <p class="text-sm font-semibold text-yellow-300 mb-1">Cara Pembayaran (Simulasi)</p>
            <p class="text-xs text-yellow-400/70">Ini adalah simulasi. Klik tombol di bawah untuk mensimulasikan konfirmasi pembayaran.</p>
        </div>
        <form action="{{ route('payment.callback', $payment->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn-success w-full py-3 rounded-xl text-sm font-bold"
                onclick="return confirm('Simulasi: Konfirmasi bahwa pembayaran telah dilakukan?')">
                <i class="fas fa-check-circle mr-2"></i>Konfirmasi Pembayaran (Simulasi)
            </button>
        </form>

        @elseif($payment->status === 'held')
        <div class="p-4 bg-accent-900/20 border border-accent-500/30 rounded-xl mb-4">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-lock text-accent-400"></i>
                <p class="text-sm font-semibold text-accent-300">Dana Aman Dalam Escrow</p>
            </div>
            <p class="text-xs text-accent-400/70">Dana Anda aman dan terlindungi. Cairkan dana setelah pekerjaan disetujui.</p>
        </div>

        @if($project->status === 'in_review' && $project->workroom?->status === 'approved')
        <div class="card p-5 mb-4 border-yellow-500/30">
            <h3 class="text-sm font-bold text-white mb-4">Berikan Rating & Cairkan Dana</h3>
            <form action="{{ route('payment.release', $payment->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs text-gray-400 mb-2">Rating Mahasiswa</label>
                    <div class="flex gap-2" id="starRating">
                        @for($i = 1; $i <= 5; $i++)
                        <label class="cursor-pointer">
                            <input type="radio" name="rating" value="{{ $i }}" class="hidden" {{ $i === 5 ? 'checked' : '' }}>
                            <i class="fas fa-star text-2xl text-yellow-400 star-icon" data-val="{{ $i }}"></i>
                        </label>
                        @endfor
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-xs text-gray-400 mb-2">Review (opsional)</label>
                    <textarea name="review" rows="3"
                        placeholder="Bagaimana pengalaman bekerja dengan mahasiswa ini?"
                        class="input-dark w-full text-sm resize-none"></textarea>
                </div>
                <button type="submit" class="btn-success w-full py-3 rounded-xl text-sm font-bold"
                    onclick="return confirm('Cairkan dana ke mahasiswa? Tindakan ini tidak dapat dibatalkan.')">
                    <i class="fas fa-paper-plane mr-2"></i>Cairkan Dana ke Mahasiswa
                </button>
            </form>
        </div>
        @else
        <div class="text-center py-4 text-sm text-gray-500">
            Dana akan dicairkan setelah pekerjaan disetujui di Workroom.
            <a href="{{ route('workroom.show', $project->id) }}" class="text-primary-400 hover:underline ml-1">Buka Workroom →</a>
        </div>
        @endif

        <form action="{{ route('payment.refund', $payment->id) }}" method="POST" class="mt-3">
            @csrf
            <button type="submit" class="w-full py-2 text-xs text-gray-600 hover:text-red-400 transition-colors"
                onclick="return confirm('Kembalikan dana? Tindakan ini tidak dapat dibatalkan.')">
                <i class="fas fa-undo mr-1"></i>Minta Refund Dana
            </button>
        </form>

        @elseif($payment->status === 'released')
        <div class="text-center py-6">
            <div class="w-16 h-16 bg-green-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-3xl text-green-400"></i>
            </div>
            <p class="text-base font-bold text-white">Proyek Selesai!</p>
            <p class="text-sm text-gray-500 mt-1">Dana berhasil dicairkan ke mahasiswa.</p>
            <a href="{{ route('client.dashboard') }}" class="btn-primary inline-block mt-4 px-5 py-2.5 rounded-xl text-sm font-semibold">
                Kembali ke Dashboard
            </a>
        </div>
        @endif
    </div>

    {{-- Escrow not yet created --}}
    @else
    <div class="card p-6">
        <h2 class="text-base font-bold text-white mb-2 flex items-center gap-2">
            <i class="fas fa-shield-alt text-accent-400"></i> Buat Pembayaran Escrow
        </h2>
        <p class="text-sm text-gray-500 mb-5">Dana akan ditahan dengan aman sampai pekerjaan selesai dan Anda menyetujuinya.</p>

        <div class="p-4 bg-accent-900/20 border border-accent-500/30 rounded-xl mb-5">
            <div class="space-y-2">
                @foreach(['Dana aman terlindungi selama pengerjaan', 'Mahasiswa termotivasi untuk menyelesaikan proyek', 'Refund tersedia jika proyek tidak sesuai', 'Platform fee hanya 10% dari budget'] as $benefit)
                <div class="flex items-center gap-2 text-sm text-accent-300">
                    <i class="fas fa-check-circle text-accent-400 text-xs"></i>
                    {{ $benefit }}
                </div>
                @endforeach
            </div>
        </div>

        <form action="{{ route('payment.deposit', $project->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-400 mb-2">Jumlah Pembayaran (Rp)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-500">Rp</span>
                    <input type="number" name="amount"
                        value="{{ $project->agreed_budget }}"
                        min="{{ $project->budget_min }}"
                        required
                        class="input-dark w-full text-sm pl-10 py-3">
                </div>
            </div>
            <button type="submit" class="btn-primary w-full py-3 rounded-xl text-sm font-bold">
                <i class="fas fa-lock mr-2"></i>Buat Escrow & Kunci Dana
            </button>
        </form>
    </div>
    @endif

    {{-- Escrow Explanation --}}
    <div class="card p-5">
        <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-info-circle text-primary-400"></i> Cara Kerja Escrow
        </h3>
        <div class="space-y-4">
            @foreach([
                ['icon' => 'fa-lock', 'color' => 'primary', 'title' => 'Dana Dikunci', 'desc' => 'Dana Anda ditahan aman di rekening escrow platform'],
                ['icon' => 'fa-hammer', 'color' => 'purple', 'title' => 'Pengerjaan', 'desc' => 'Mahasiswa mengerjakan proyek dengan jaminan pembayaran'],
                ['icon' => 'fa-check', 'color' => 'accent', 'title' => 'Persetujuan', 'desc' => 'Anda menyetujui hasil kerja di Workroom'],
                ['icon' => 'fa-paper-plane', 'color' => 'green', 'title' => 'Pencairan', 'desc' => 'Dana otomatis dikirim ke mahasiswa'],
            ] as $step)
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-{{ $step['color'] }}-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas {{ $step['icon'] }} text-{{ $step['color'] }}-400 text-xs"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-white">{{ $step['title'] }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $step['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
