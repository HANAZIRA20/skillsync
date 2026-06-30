@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, ' . $user->name . ' 👋')

@section('content')
<div class="space-y-6 animate-fade-in">

    {{-- KRS Warning --}}
    @if($stats['krs_status'] === 'not_uploaded')
    <div class="p-4 bg-amber-900/30 border border-amber-500/40 rounded-xl flex items-center gap-4">
        <div class="w-10 h-10 bg-amber-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-amber-400"></i>
        </div>
        <div class="flex-1">
            <p class="text-sm font-semibold text-amber-300">KRS Belum Diupload</p>
            <p class="text-xs text-amber-400/70 mt-0.5">Upload KRS Anda agar AI dapat mendeteksi skill dan mencarikan proyek yang sesuai.</p>
        </div>
        <a href="{{ route('student.krs') }}" class="btn-primary px-4 py-2 rounded-xl text-sm font-semibold flex-shrink-0">
            Upload Sekarang
        </a>
    </div>
    @endif

    {{-- Stats Row --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-primary-500/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-briefcase text-primary-400"></i>
                </div>
                <span class="text-xs text-gray-500">Total</span>
            </div>
            <div class="text-2xl font-black text-white">{{ $stats['total_projects'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Proyek Selesai</div>
        </div>

        <div class="stat-card card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-accent-500/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-accent-400"></i>
                </div>
                <span class="text-xs text-gray-500">Total</span>
            </div>
            <div class="text-2xl font-black text-white">Rp {{ number_format($stats['total_earnings'], 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-1">Total Pendapatan</div>
        </div>

        <div class="stat-card card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-yellow-500/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-star text-yellow-400"></i>
                </div>
                <span class="text-xs text-gray-500">Rating</span>
            </div>
            <div class="text-2xl font-black text-white">{{ number_format($stats['average_rating'], 1) }}</div>
            <div class="text-xs text-gray-500 mt-1">Rating Rata-rata</div>
        </div>

        <div class="stat-card card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-purple-500/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-code text-purple-400"></i>
                </div>
                <span class="text-xs text-gray-500">Skill</span>
            </div>
            <div class="text-2xl font-black text-white">{{ $stats['total_skills'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Skill Terdeteksi</div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Active Projects --}}
        <div class="lg:col-span-2 card p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base font-bold text-white">Proyek Aktif</h2>
                <a href="{{ route('student.projects') }}" class="text-xs text-primary-400 hover:text-primary-300 transition-colors">Lihat Semua →</a>
            </div>

            @if($activeProjects->isEmpty())
            <div class="text-center py-10">
                <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-inbox text-2xl text-gray-600"></i>
                </div>
                <p class="text-gray-500 text-sm">Belum ada proyek aktif</p>
                <p class="text-gray-600 text-xs mt-1">Cek rekomendasi proyek di bawah</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach($activeProjects as $project)
                <a href="{{ route('workroom.show', $project->id) }}" class="flex items-center gap-4 p-4 bg-white/3 hover:bg-white/5 rounded-xl transition-all border border-white/5 hover:border-primary-500/30 group">
                    <div class="w-10 h-10 bg-primary-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-code text-primary-400 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate group-hover:text-primary-300 transition-colors">{{ $project->title }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $project->client->user->name }} · Deadline {{ \Carbon\Carbon::parse($project->deadline)->diffForHumans() }}</p>
                    </div>
                    <div>
                        @php
                            $statusBadge = [
                                'in_progress' => ['class' => 'badge-progress', 'label' => 'In Progress'],
                                'in_review' => ['class' => 'badge-review', 'label' => 'Review'],
                                'revision' => ['class' => 'badge-revision', 'label' => 'Revisi'],
                            ][$project->status] ?? ['class' => 'badge-open', 'label' => $project->status];
                        @endphp
                        <span class="badge {{ $statusBadge['class'] }}">{{ $statusBadge['label'] }}</span>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Profile Completion --}}
        <div class="card p-6">
            <h2 class="text-base font-bold text-white mb-5">Profil Saya</h2>
            @php
                $profileScore = 0;
                if($student) {
                    if($student->bio) $profileScore += 20;
                    if($student->linkedin_url) $profileScore += 20;
                    if($student->github_url) $profileScore += 20;
                    if($stats['krs_status'] !== 'not_uploaded') $profileScore += 20;
                    if($stats['total_skills'] > 0) $profileScore += 20;
                }
            @endphp
            <div class="flex items-center justify-center mb-5">
                <div class="relative w-28 h-28">
                    <svg class="w-28 h-28 -rotate-90" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="50" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="10"/>
                        <circle cx="60" cy="60" r="50" fill="none" stroke="#6366f1" stroke-width="10"
                            stroke-dasharray="{{ 314 * $profileScore / 100 }} 314"
                            stroke-linecap="round" style="transition:stroke-dasharray 1s ease"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl font-black text-white">{{ $profileScore }}%</span>
                        <span class="text-xs text-gray-500">Lengkap</span>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                @php
                    $checks = [
                        ['label' => 'Bio tersedia', 'done' => $student?->bio],
                        ['label' => 'LinkedIn terhubung', 'done' => $student?->linkedin_url],
                        ['label' => 'GitHub terhubung', 'done' => $student?->github_url],
                        ['label' => 'KRS diupload', 'done' => $stats['krs_status'] !== 'not_uploaded'],
                        ['label' => 'Skills terdeteksi', 'done' => $stats['total_skills'] > 0],
                    ];
                @endphp
                @foreach($checks as $check)
                <div class="flex items-center gap-3 text-sm">
                    <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0 {{ $check['done'] ? 'bg-accent-500/20' : 'bg-white/5' }}">
                        <i class="fas fa-{{ $check['done'] ? 'check text-accent-400' : 'times text-gray-600' }} text-xs"></i>
                    </div>
                    <span class="{{ $check['done'] ? 'text-gray-300' : 'text-gray-600' }}">{{ $check['label'] }}</span>
                </div>
                @endforeach
            </div>

            <a href="{{ route('student.profile') }}" class="btn-outline w-full mt-5 py-2.5 rounded-xl text-sm font-semibold text-center block">
                Lengkapi Profil
            </a>
        </div>
    </div>

    {{-- AI Recommendations --}}
    @if($recentMatchings->isNotEmpty())
    <div class="card p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-8 h-8 bg-primary-500/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-robot text-primary-400 text-sm"></i>
            </div>
            <h2 class="text-base font-bold text-white">Rekomendasi AI untuk Anda</h2>
            <span class="badge badge-progress ml-auto">{{ $recentMatchings->count() }} Proyek</span>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($recentMatchings as $matching)
            <div class="p-4 bg-white/3 rounded-xl border border-white/5 hover:border-primary-500/30 transition-all group">
                <div class="flex items-start justify-between mb-3">
                    <h3 class="text-sm font-semibold text-white group-hover:text-primary-300 transition-colors line-clamp-2 flex-1">
                        {{ $matching->project->title }}
                    </h3>
                    <div class="flex-shrink-0 ml-2 text-center">
                        <div class="text-lg font-black text-primary-400">{{ round($matching->match_score) }}%</div>
                        <div class="text-[10px] text-gray-600">Match</div>
                    </div>
                </div>
                <div class="progress-bar h-1.5 mb-3">
                    <div class="progress-fill bg-gradient-to-r from-primary-600 to-accent-500" style="width: {{ $matching->match_score }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mb-3">{{ $matching->project->category }} · Rp {{ number_format($matching->project->budget_max, 0, ',', '.') }}</p>
                <div class="flex flex-wrap gap-1 mb-3">
                    @foreach(array_slice($matching->project->required_skills ?? [], 0, 3) as $skill)
                    <span class="text-[10px] px-2 py-0.5 bg-white/5 text-gray-400 rounded-full border border-white/5">{{ $skill }}</span>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Open Projects --}}
    @if($recommendedProjects->isNotEmpty())
    <div class="card p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-base font-bold text-white">Proyek Terbuka</h2>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            @foreach($recommendedProjects as $project)
            <div class="p-4 bg-white/3 rounded-xl border border-white/5 hover:border-accent-500/30 transition-all">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 bg-accent-500/15 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-rocket text-accent-400 text-xs"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-white line-clamp-1">{{ $project->title }}</h3>
                        <p class="text-xs text-gray-500">{{ $project->category }}</p>
                    </div>
                    <span class="badge badge-open ml-auto">Open</span>
                </div>
                <p class="text-xs text-gray-400 line-clamp-2 mb-3">{{ Str::limit($project->description, 100) }}</p>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-accent-400">Rp {{ number_format($project->budget_max, 0, ',', '.') }}</span>
                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
