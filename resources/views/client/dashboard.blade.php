@extends('layouts.app')

@section('title', 'Dashboard Client')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, ' . $user->name)

@section('content')
<div class="space-y-6 animate-fade-in">

    {{-- Stats Row --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-primary-500/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-folder-plus text-primary-400"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-white">{{ $stats['total_posted'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Total Proyek Dibuat</div>
        </div>

        <div class="stat-card card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-accent-500/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-double text-accent-400"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-white">{{ $stats['total_completed'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Proyek Selesai</div>
        </div>

        <div class="stat-card card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-yellow-500/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-spinner text-yellow-400"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-white">{{ $stats['active_projects'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Proyek Aktif</div>
        </div>

        <div class="stat-card card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-green-500/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-wallet text-green-400"></i>
                </div>
            </div>
            <div class="text-xl font-black text-white">Rp {{ number_format($stats['total_spent'], 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-1">Total Pengeluaran</div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Recent Projects --}}
        <div class="lg:col-span-2 card p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base font-bold text-white">Proyek Terkini</h2>
                <div class="flex gap-2">
                    <a href="{{ route('client.projects') }}" class="text-xs text-gray-500 hover:text-gray-300 transition-colors">Semua Proyek →</a>
                    <a href="{{ route('client.create-project') }}" class="btn-primary px-3 py-1.5 rounded-xl text-xs font-semibold ml-2">
                        <i class="fas fa-plus mr-1"></i>Buat Proyek
                    </a>
                </div>
            </div>

            @if($recentProjects->isEmpty())
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-folder-open text-2xl text-gray-600"></i>
                </div>
                <p class="text-gray-500 text-sm">Belum ada proyek</p>
                <a href="{{ route('client.create-project') }}" class="btn-primary inline-block mt-4 px-4 py-2 rounded-xl text-sm font-semibold">
                    <i class="fas fa-plus mr-2"></i>Buat Proyek Pertama
                </a>
            </div>
            @else
            <div class="space-y-3">
                @foreach($recentProjects as $project)
                <a href="{{ route('client.project-detail', $project->id) }}" class="flex items-center gap-4 p-4 bg-white/3 hover:bg-white/5 rounded-xl transition-all border border-white/5 hover:border-primary-500/30 group">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                        @if($project->status === 'completed') bg-accent-500/20
                        @elseif(in_array($project->status, ['in_progress', 'in_review'])) bg-primary-500/20
                        @else bg-yellow-500/20 @endif">
                        <i class="fas
                            @if($project->status === 'completed') fa-check text-accent-400
                            @elseif(in_array($project->status, ['in_progress', 'in_review'])) fa-code text-primary-400
                            @else fa-clock text-yellow-400 @endif text-sm">
                        </i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate group-hover:text-primary-300 transition-colors">{{ $project->title }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            @if($project->selectedStudent)
                                Dikerjakan oleh: <span class="text-gray-400">{{ $project->selectedStudent->user->name }}</span>
                            @else
                                <span class="text-gray-600">Menunggu kandidat</span>
                            @endif
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        @php
                            $badge = [
                                'open' => ['class' => 'badge-open', 'label' => 'Open'],
                                'waiting_payment' => ['class' => 'badge-waiting', 'label' => 'Tunggu Bayar'],
                                'in_progress' => ['class' => 'badge-progress', 'label' => 'In Progress'],
                                'in_review' => ['class' => 'badge-review', 'label' => 'Review'],
                                'revision' => ['class' => 'badge-revision', 'label' => 'Revisi'],
                                'completed' => ['class' => 'badge-completed', 'label' => 'Selesai'],
                                'disputed' => ['class' => 'badge-disputed', 'label' => 'Sengketa'],
                            ][$project->status] ?? ['class' => 'badge-open', 'label' => $project->status];
                        @endphp
                        <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Quick Actions + Company Info --}}
        <div class="space-y-4">
            {{-- Company Card --}}
            <div class="card p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-accent-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-xl font-black text-white">{{ strtoupper(substr($client?->company_name ?? $user->name, 0, 1)) }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-white">{{ $client?->company_name ?? $user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $client?->industry ?? 'Client' }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('client.projects') }}" class="btn-outline p-3 rounded-xl text-center transition-all">
                        <i class="fas fa-folder-open block text-lg text-primary-400 mb-1"></i>
                        <span class="text-xs text-gray-400">Proyek</span>
                    </a>
                    <a href="{{ route('client.create-project') }}" class="btn-primary p-3 rounded-xl text-center">
                        <i class="fas fa-plus block text-lg mb-1"></i>
                        <span class="text-xs">Baru</span>
                    </a>
                </div>
            </div>

            {{-- AI Engine Status --}}
            <div class="card p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-2 h-2 bg-accent-400 rounded-full animate-pulse"></div>
                    <span class="text-xs font-semibold text-gray-400">AI Matching Engine</span>
                </div>
                <p class="text-xs text-gray-500 mb-3">AI kami secara otomatis mencarikan mahasiswa terbaik berdasarkan skill, rating, dan riwayat proyek.</p>
                <div class="space-y-2">
                    @foreach(['Multi-criteria Scoring', 'KRS-based Skill Detection', 'Portfolio Analysis'] as $feature)
                    <div class="flex items-center gap-2 text-xs text-gray-400">
                        <i class="fas fa-check-circle text-accent-400 text-xs"></i>
                        {{ $feature }}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
