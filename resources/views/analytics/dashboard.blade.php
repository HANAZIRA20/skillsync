@extends('layouts.app')

@section('title', 'Analytics OLAP')
@section('page-title', 'Analytics Dashboard')
@section('page-subtitle', 'Insight mendalam platform SkillSync dengan OLAP queries')

@section('content')
<div class="space-y-6 animate-fade-in">

    {{-- Top Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card card p-5">
            <div class="w-10 h-10 bg-primary-500/20 rounded-xl flex items-center justify-center mb-3">
                <i class="fas fa-users text-primary-400"></i>
            </div>
            <div class="text-2xl font-black text-white">{{ $studentAnalytics['totalStudents'] }}</div>
            <div class="text-xs text-gray-400 mt-0.5">Total Mahasiswa</div>
            <div class="text-[10px] text-gray-600 mt-0.5">{{ $studentAnalytics['activeStudents'] }} aktif (KRS uploaded)</div>
        </div>
        <div class="stat-card card p-5">
            <div class="w-10 h-10 bg-purple-500/20 rounded-xl flex items-center justify-center mb-3">
                <i class="fas fa-bullseye text-purple-400"></i>
            </div>
            <div class="text-2xl font-black text-white">{{ $matchingAnalytics['totalMatches'] }}</div>
            <div class="text-xs text-gray-400 mt-0.5">Total AI Matchings</div>
            <div class="text-[10px] text-gray-600 mt-0.5">{{ $matchingAnalytics['acceptanceRate'] }}% acceptance rate</div>
        </div>
        <div class="stat-card card p-5">
            <div class="w-10 h-10 bg-yellow-500/20 rounded-xl flex items-center justify-center mb-3">
                <i class="fas fa-coins text-yellow-400"></i>
            </div>
            <div class="text-xl font-black text-white">Rp {{ number_format($revenueAnalytics['totalRevenue'], 0, ',', '.') }}</div>
            <div class="text-xs text-gray-400 mt-0.5">Platform Revenue</div>
            <div class="text-[10px] text-gray-600 mt-0.5">{{ $revenueAnalytics['successfulPayments'] }} transaksi sukses</div>
        </div>
        <div class="stat-card card p-5">
            <div class="w-10 h-10 bg-accent-500/20 rounded-xl flex items-center justify-center mb-3">
                <i class="fas fa-briefcase text-accent-400"></i>
            </div>
            <div class="text-2xl font-black text-white">{{ $projectAnalytics['totalProjects'] }}</div>
            <div class="text-xs text-gray-400 mt-0.5">Total Proyek</div>
            <div class="text-[10px] text-gray-600 mt-0.5">{{ $projectAnalytics['completedProjects'] }} selesai</div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Project Status Distribution --}}
        <div class="card p-6">
            <h2 class="text-sm font-bold text-white mb-5 flex items-center gap-2">
                <i class="fas fa-chart-pie text-primary-400"></i> Distribusi Status Proyek
            </h2>
            @php
                $statusColors = [
                    'open' => ['bar' => 'bg-accent-500', 'label' => 'Open'],
                    'waiting_payment' => ['bar' => 'bg-yellow-500', 'label' => 'Menunggu Bayar'],
                    'in_progress' => ['bar' => 'bg-primary-500', 'label' => 'In Progress'],
                    'in_review' => ['bar' => 'bg-orange-500', 'label' => 'Review'],
                    'revision' => ['bar' => 'bg-red-400', 'label' => 'Revisi'],
                    'completed' => ['bar' => 'bg-green-500', 'label' => 'Selesai'],
                    'cancelled' => ['bar' => 'bg-gray-600', 'label' => 'Dibatalkan'],
                    'disputed' => ['bar' => 'bg-red-600', 'label' => 'Sengketa'],
                ];
                $totalP = array_sum($projectAnalytics['statusDistribution']);
            @endphp
            <div class="space-y-3">
                @foreach($projectAnalytics['statusDistribution'] as $status => $count)
                @php
                    $config = $statusColors[$status] ?? ['bar' => 'bg-gray-500', 'label' => $status];
                    $pct = $totalP > 0 ? round(($count / $totalP) * 100) : 0;
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-gray-400">{{ $config['label'] }}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-white">{{ $count }}</span>
                            <span class="text-[10px] text-gray-600">({{ $pct }}%)</span>
                        </div>
                    </div>
                    <div class="progress-bar h-2">
                        <div class="progress-fill {{ $config['bar'] }}" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- AI Match Score Distribution --}}
        <div class="card p-6">
            <h2 class="text-sm font-bold text-white mb-5 flex items-center gap-2">
                <i class="fas fa-robot text-purple-400"></i> Distribusi AI Score
            </h2>
            <div class="space-y-3 mb-5">
                @php
                    $scoreColors = ['90-100' => 'bg-accent-500', '75-89' => 'bg-primary-500', '60-74' => 'bg-yellow-500', '40-59' => 'bg-orange-500', '0-39' => 'bg-red-500'];
                    $totalM = array_sum($matchingAnalytics['scoreDistribution']);
                @endphp
                @foreach($matchingAnalytics['scoreDistribution'] as $range => $count)
                @php $pct = $totalM > 0 ? round(($count / $totalM) * 100) : 0; @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-gray-400">Score {{ $range }}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-white">{{ $count }}</span>
                            <span class="text-[10px] text-gray-600">({{ $pct }}%)</span>
                        </div>
                    </div>
                    <div class="progress-bar h-2">
                        <div class="progress-fill {{ $scoreColors[$range] ?? 'bg-gray-500' }}" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="grid grid-cols-2 gap-3 pt-3 border-t border-white/5">
                <div class="text-center">
                    <div class="text-xl font-black text-primary-400">{{ round($matchingAnalytics['avgScore'] ?? 0, 1) }}%</div>
                    <div class="text-xs text-gray-500">Rata-rata Score</div>
                </div>
                <div class="text-center">
                    <div class="text-xl font-black text-accent-400">{{ $matchingAnalytics['acceptanceRate'] }}%</div>
                    <div class="text-xs text-gray-500">Acceptance Rate</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Category Popularity --}}
        <div class="card p-6">
            <h2 class="text-sm font-bold text-white mb-5 flex items-center gap-2">
                <i class="fas fa-tags text-accent-400"></i> Kategori Proyek Terpopuler
            </h2>
            @if($projectAnalytics['categoryPopularity']->isEmpty())
            <div class="text-center py-8 text-gray-600 text-sm">Belum ada data</div>
            @else
            @php $maxCat = $projectAnalytics['categoryPopularity']->max('count') ?: 1; @endphp
            <div class="space-y-3">
                @foreach($projectAnalytics['categoryPopularity'] as $cat)
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-gray-400">{{ $cat->category }}</span>
                        <span class="text-xs font-bold text-white">{{ $cat->count }}</span>
                    </div>
                    <div class="progress-bar h-1.5">
                        <div class="progress-fill bg-gradient-to-r from-accent-600 to-accent-400"
                            style="width: {{ round(($cat->count / $maxCat) * 100) }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Top Skills in AI Matching --}}
        <div class="card p-6">
            <h2 class="text-sm font-bold text-white mb-5 flex items-center gap-2">
                <i class="fas fa-brain text-yellow-400"></i> Skill Paling Dicari
            </h2>
            @if(empty($matchingAnalytics['topSkills']))
            <div class="text-center py-8 text-gray-600 text-sm">Belum ada data skill</div>
            @else
            @php $maxSkill = max($matchingAnalytics['topSkills']); @endphp
            <div class="space-y-3">
                @foreach($matchingAnalytics['topSkills'] as $skill => $count)
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-gray-400">{{ $skill }}</span>
                        <span class="text-xs font-bold text-white">{{ $count }}</span>
                    </div>
                    <div class="progress-bar h-1.5">
                        <div class="progress-fill bg-gradient-to-r from-yellow-600 to-yellow-400"
                            style="width: {{ round(($count / $maxSkill) * 100) }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Revenue by Category --}}
        <div class="card p-6">
            <h2 class="text-sm font-bold text-white mb-5 flex items-center gap-2">
                <i class="fas fa-chart-bar text-green-400"></i> Revenue per Kategori
            </h2>
            @if($revenueAnalytics['revenueByCategory']->isEmpty())
            <div class="text-center py-8 text-gray-600 text-sm">Belum ada data transaksi</div>
            @else
            @php $maxRev = $revenueAnalytics['revenueByCategory']->max('revenue') ?: 1; @endphp
            <div class="space-y-3">
                @foreach($revenueAnalytics['revenueByCategory'] as $rev)
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-gray-400">{{ $rev->project_category }}</span>
                        <span class="text-xs font-bold text-accent-400">Rp {{ number_format($rev->revenue, 0, ',', '.') }}</span>
                    </div>
                    <div class="progress-bar h-1.5">
                        <div class="progress-fill bg-gradient-to-r from-green-600 to-accent-500"
                            style="width: {{ round(($rev->revenue / $maxRev) * 100) }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Client Industry Distribution --}}
        <div class="card p-6">
            <h2 class="text-sm font-bold text-white mb-5 flex items-center gap-2">
                <i class="fas fa-building text-blue-400"></i> Industri Client
            </h2>
            @if($clientAnalytics['industryDistribution']->isEmpty())
            <div class="text-center py-8 text-gray-600 text-sm">Belum ada data</div>
            @else
            @php $maxInd = $clientAnalytics['industryDistribution']->max('count') ?: 1; @endphp
            <div class="space-y-3">
                @foreach($clientAnalytics['industryDistribution'] as $ind)
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-gray-400">{{ $ind->industry }}</span>
                        <span class="text-xs font-bold text-white">{{ $ind->count }}</span>
                    </div>
                    <div class="progress-bar h-1.5">
                        <div class="progress-fill bg-gradient-to-r from-blue-600 to-blue-400"
                            style="width: {{ round(($ind->count / $maxInd) * 100) }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- Top Students --}}
    @if($studentAnalytics['topStudents']->isNotEmpty())
    <div class="card overflow-hidden">
        <div class="p-5 border-b border-white/5">
            <h2 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fas fa-trophy text-yellow-400"></i> Top Mahasiswa Berdasarkan Proyek
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">#</th>
                        <th class="text-left">Mahasiswa</th>
                        <th class="text-left">Universitas</th>
                        <th class="text-left">Total Proyek</th>
                        <th class="text-left">Total Penghasilan</th>
                        <th class="text-left">Avg Rating</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($studentAnalytics['topStudents'] as $index => $student)
                    <tr>
                        <td>
                            <span class="{{ $index < 3 ? 'text-yellow-400 font-black' : 'text-gray-500' }}">
                                {{ $index < 3 ? ['🥇','🥈','🥉'][$index] : ($index + 1) }}
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-primary-500/20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-primary-400 text-xs"></i>
                                </div>
                                <span class="text-sm text-white">ID #{{ $student->student_id }}</span>
                            </div>
                        </td>
                        <td class="text-sm text-gray-400">{{ $student->student_universitas ?? '—' }}</td>
                        <td class="text-sm font-bold text-white">{{ $student->total_projects }}</td>
                        <td class="text-sm font-bold text-accent-400">Rp {{ number_format($student->total_earned, 0, ',', '.') }}</td>
                        <td>
                            <div class="flex items-center gap-1">
                                <i class="fas fa-star text-yellow-400 text-xs"></i>
                                <span class="text-sm text-white">{{ number_format($student->avg_rating ?? 0, 1) }}</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Revenue Summary --}}
    <div class="grid lg:grid-cols-3 gap-4">
        @foreach([
            ['label' => 'Dana Ditahan Escrow', 'value' => 'Rp ' . number_format($revenueAnalytics['heldAmount'], 0, ',', '.'), 'icon' => 'fa-lock', 'color' => 'primary'],
            ['label' => 'Total Refund', 'value' => 'Rp ' . number_format($revenueAnalytics['totalRefunds'], 0, ',', '.'), 'icon' => 'fa-undo', 'color' => 'red', 'sub' => $revenueAnalytics['refundCount'] . 'x transaksi'],
            ['label' => 'Avg Durasi Proyek', 'value' => round($projectAnalytics['avgDuration'] ?? 0) . ' hari', 'icon' => 'fa-calendar', 'color' => 'accent'],
        ] as $card)
        <div class="card p-5">
            <div class="w-10 h-10 bg-{{ $card['color'] }}-500/20 rounded-xl flex items-center justify-center mb-3">
                <i class="fas {{ $card['icon'] }} text-{{ $card['color'] }}-400"></i>
            </div>
            <div class="text-xl font-black text-white">{{ $card['value'] }}</div>
            <div class="text-xs text-gray-400 mt-0.5">{{ $card['label'] }}</div>
            @if(isset($card['sub']))
            <div class="text-[10px] text-gray-600 mt-0.5">{{ $card['sub'] }}</div>
            @endif
        </div>
        @endforeach
    </div>

</div>
@endsection
