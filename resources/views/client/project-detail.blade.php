@extends('layouts.app')

@section('title', 'Detail Proyek')
@section('page-title', $project->title)
@section('page-subtitle', 'Detail dan status proyek')

@section('content')
<div class="space-y-6 animate-fade-in">

    {{-- Back Button --}}
    <a href="{{ route('client.projects') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-300 transition-colors">
        <i class="fas fa-arrow-left text-xs"></i> Kembali ke Proyek
    </a>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Main Info --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="card p-6">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div>
                        <h1 class="text-lg font-bold text-white">{{ $project->title }}</h1>
                        <p class="text-sm text-gray-500 mt-1">{{ $project->category }}</p>
                    </div>
                    @php
                        $badge = [
                            'open' => ['class' => 'badge-open', 'label' => 'Open'],
                            'waiting_payment' => ['class' => 'badge-waiting', 'label' => 'Menunggu Pembayaran'],
                            'in_progress' => ['class' => 'badge-progress', 'label' => 'In Progress'],
                            'in_review' => ['class' => 'badge-review', 'label' => 'Dalam Review'],
                            'revision' => ['class' => 'badge-revision', 'label' => 'Revisi'],
                            'completed' => ['class' => 'badge-completed', 'label' => 'Selesai ✓'],
                            'disputed' => ['class' => 'badge-disputed', 'label' => 'Sengketa'],
                        ][$project->status] ?? ['class' => 'badge-open', 'label' => $project->status];
                    @endphp
                    <span class="badge {{ $badge['class'] }} text-xs px-3 py-1.5 flex-shrink-0">{{ $badge['label'] }}</span>
                </div>

                <div class="grid grid-cols-3 gap-4 p-4 bg-white/3 rounded-xl border border-white/5 mb-5">
                    <div class="text-center">
                        <div class="text-lg font-black text-white">Rp {{ number_format($project->agreed_budget ?? $project->budget_max, 0, ',', '.') }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">Budget</div>
                    </div>
                    <div class="text-center border-x border-white/5">
                        <div class="text-lg font-black text-white">{{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">Deadline</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-black text-white">{{ $project->max_revisions }}x</div>
                        <div class="text-xs text-gray-500 mt-0.5">Maks. Revisi</div>
                    </div>
                </div>

                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Deskripsi</h3>
                <p class="text-sm text-gray-300 leading-relaxed">{{ $project->description }}</p>

                <div class="mt-5">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Skill Dibutuhkan</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($project->required_skills ?? [] as $skill)
                        <span class="text-xs px-3 py-1 bg-primary-500/15 text-primary-300 rounded-full border border-primary-500/25">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="card p-5">
                <h3 class="text-sm font-bold text-white mb-4">Tindakan</h3>
                <div class="flex flex-wrap gap-3">
                    @if($project->status === 'open')
                    <a href="{{ route('client.candidates', $project->id) }}" class="btn-primary px-5 py-2.5 rounded-xl text-sm font-semibold">
                        <i class="fas fa-users mr-2"></i>Lihat Kandidat AI
                    </a>
                    @elseif($project->status === 'waiting_payment')
                    <a href="{{ route('payment.escrow', $project->id) }}" class="btn-success px-5 py-2.5 rounded-xl text-sm font-semibold">
                        <i class="fas fa-credit-card mr-2"></i>Lakukan Pembayaran Escrow
                    </a>
                    @elseif(in_array($project->status, ['in_progress', 'in_review', 'revision']))
                    <a href="{{ route('workroom.show', $project->id) }}" class="btn-primary px-5 py-2.5 rounded-xl text-sm font-semibold">
                        <i class="fas fa-door-open mr-2"></i>Buka Workroom
                    </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">
            {{-- Selected Student --}}
            @if($project->selectedStudent)
            <div class="card p-5">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Mahasiswa Terpilih</h3>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-lg font-black text-white">{{ strtoupper(substr($project->selectedStudent->user->name, 0, 1)) }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-white">{{ $project->selectedStudent->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $project->selectedStudent->university }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="text-center p-2 bg-white/3 rounded-lg">
                        <div class="text-base font-black text-white">{{ $project->selectedStudent->total_projects }}</div>
                        <div class="text-[10px] text-gray-500">Proyek</div>
                    </div>
                    <div class="text-center p-2 bg-white/3 rounded-lg">
                        <div class="text-base font-black text-white">{{ number_format($project->selectedStudent->average_rating, 1) }}</div>
                        <div class="text-[10px] text-gray-500">Rating</div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Payment Status --}}
            @if($project->payment)
            <div class="card p-5">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Status Pembayaran</h3>
                @php
                    $payBadge = [
                        'pending' => ['class' => 'badge-waiting', 'label' => 'Pending'],
                        'held' => ['class' => 'badge-progress', 'label' => 'Dana Ditahan'],
                        'released' => ['class' => 'badge-completed', 'label' => 'Dana Dicairkan'],
                        'refunded' => ['class' => 'badge-revision', 'label' => 'Dikembalikan'],
                    ][$project->payment->status] ?? ['class' => 'badge-open', 'label' => $project->payment->status];
                @endphp
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm text-gray-400">Status</span>
                    <span class="badge {{ $payBadge['class'] }}">{{ $payBadge['label'] }}</span>
                </div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm text-gray-400">Jumlah</span>
                    <span class="text-sm font-bold text-white">Rp {{ number_format($project->payment->amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-400">Platform Fee</span>
                    <span class="text-sm text-gray-300">Rp {{ number_format($project->payment->platform_fee, 0, ',', '.') }}</span>
                </div>
            </div>
            @endif

            {{-- Project Timeline --}}
            <div class="card p-5">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Timeline</h3>
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 bg-primary-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check text-primary-400 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-300">Proyek Dibuat</p>
                            <p class="text-[10px] text-gray-600">{{ $project->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    @if($project->selectedStudent)
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 bg-accent-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check text-accent-400 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-300">Kandidat Dipilih</p>
                            <p class="text-[10px] text-gray-600">{{ $project->selectedStudent->name ?? '—' }}</p>
                        </div>
                    </div>
                    @endif
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 {{ $project->status === 'completed' ? 'bg-accent-500/20' : 'bg-white/5' }} rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-flag {{ $project->status === 'completed' ? 'text-accent-400' : 'text-gray-600' }} text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium {{ $project->status === 'completed' ? 'text-gray-300' : 'text-gray-600' }}">Deadline</p>
                            <p class="text-[10px] text-gray-600">{{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
