@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')
@section('page-subtitle', 'Pantau dan kelola platform SkillSync')

@section('content')
<div class="space-y-6 animate-fade-in">

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'Total User', 'value' => $stats['total_users'], 'icon' => 'fa-users', 'color' => 'primary', 'sub' => $stats['total_students'] . ' mahasiswa, ' . $stats['total_clients'] . ' client'],
            ['label' => 'Total Proyek', 'value' => $stats['total_projects'], 'icon' => 'fa-briefcase', 'color' => 'purple', 'sub' => $stats['active_projects'] . ' aktif'],
            ['label' => 'Revenue Platform', 'value' => 'Rp ' . number_format($stats['total_revenue'], 0, ',', '.'), 'icon' => 'fa-coins', 'color' => 'yellow', 'sub' => $stats['completed_projects'] . ' proyek selesai'],
            ['label' => 'Dana Ditahan', 'value' => 'Rp ' . number_format($stats['held_amount'], 0, ',', '.'), 'icon' => 'fa-shield-alt', 'color' => 'accent', 'sub' => 'Dalam escrow'],
        ] as $stat)
        <div class="stat-card card p-5">
            <div class="w-10 h-10 bg-{{ $stat['color'] }}-500/20 rounded-xl flex items-center justify-center mb-3">
                <i class="fas {{ $stat['icon'] }} text-{{ $stat['color'] }}-400"></i>
            </div>
            <div class="text-xl font-black text-white">{{ $stat['value'] }}</div>
            <div class="text-xs font-semibold text-gray-400 mt-0.5">{{ $stat['label'] }}</div>
            <div class="text-[10px] text-gray-600 mt-0.5">{{ $stat['sub'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Disputes Alert --}}
    @if($stats['disputed_projects'] > 0)
    <div class="p-4 bg-red-900/30 border border-red-500/40 rounded-xl flex items-center gap-4">
        <div class="w-10 h-10 bg-red-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-red-400"></i>
        </div>
        <div class="flex-1">
            <p class="text-sm font-bold text-red-300">{{ $stats['disputed_projects'] }} Proyek dalam Sengketa!</p>
            <p class="text-xs text-red-400/70">Memerlukan tindakan admin segera.</p>
        </div>
        <a href="#disputes" class="btn-primary px-4 py-2 rounded-xl text-sm font-semibold flex-shrink-0 bg-red-500 hover:bg-red-600" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
            Tangani Sekarang
        </a>
    </div>
    @endif

    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Recent Users --}}
        <div class="card overflow-hidden">
            <div class="p-5 border-b border-white/5">
                <h2 class="text-sm font-bold text-white">User Terbaru</h2>
            </div>
            <div class="divide-y divide-white/5">
                @forelse($recentUsers as $u)
                <div class="flex items-center gap-3 px-5 py-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold
                        {{ $u->role === 'mahasiswa' ? 'bg-primary-500/20 text-primary-400' : ($u->role === 'client' ? 'bg-accent-500/20 text-accent-400' : 'bg-yellow-500/20 text-yellow-400') }}">
                        {{ strtoupper(substr($u->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ $u->name }}</p>
                        <p class="text-xs text-gray-600 truncate">{{ $u->email }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="text-[10px] px-2 py-0.5 rounded-full
                            {{ $u->role === 'mahasiswa' ? 'bg-primary-500/20 text-primary-400' : ($u->role === 'client' ? 'bg-accent-500/20 text-accent-400' : 'bg-yellow-500/20 text-yellow-400') }}">
                            {{ ucfirst($u->role) }}
                        </span>
                        <p class="text-[10px] text-gray-600 mt-0.5">{{ $u->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-sm text-gray-600">Tidak ada data</div>
                @endforelse
            </div>
        </div>

        {{-- Recent Projects --}}
        <div class="card overflow-hidden">
            <div class="p-5 border-b border-white/5">
                <h2 class="text-sm font-bold text-white">Proyek Terkini</h2>
            </div>
            <div class="divide-y divide-white/5">
                @forelse($recentProjects as $project)
                <div class="flex items-center gap-3 px-5 py-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ $project->title }}</p>
                        <p class="text-xs text-gray-600">{{ $project->client?->user?->name ?? '—' }}</p>
                    </div>
                    @php
                        $badge = [
                            'open' => ['class' => 'badge-open', 'label' => 'Open'],
                            'waiting_payment' => ['class' => 'badge-waiting', 'label' => 'Bayar'],
                            'in_progress' => ['class' => 'badge-progress', 'label' => 'Progress'],
                            'in_review' => ['class' => 'badge-review', 'label' => 'Review'],
                            'completed' => ['class' => 'badge-completed', 'label' => 'Selesai'],
                            'disputed' => ['class' => 'badge-disputed', 'label' => 'Sengketa'],
                        ][$project->status] ?? ['class' => 'badge-open', 'label' => $project->status];
                    @endphp
                    <span class="badge {{ $badge['class'] }} flex-shrink-0">{{ $badge['label'] }}</span>
                </div>
                @empty
                <div class="p-8 text-center text-sm text-gray-600">Tidak ada data</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Disputed Projects --}}
    @if($disputedProjects->isNotEmpty())
    <div class="card overflow-hidden" id="disputes">
        <div class="p-5 border-b border-red-500/20 bg-red-900/10">
            <h2 class="text-sm font-bold text-red-300 flex items-center gap-2">
                <i class="fas fa-flag text-red-400"></i> Proyek dalam Sengketa — Perlu Tindakan Admin
            </h2>
        </div>
        <div class="divide-y divide-white/5">
            @foreach($disputedProjects as $project)
            <div class="p-5">
                <div class="flex items-start gap-4">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-white">{{ $project->title }}</h3>
                        <div class="flex gap-4 mt-1">
                            <p class="text-xs text-gray-500">Client: <span class="text-gray-400">{{ $project->client?->user?->name }}</span></p>
                            <p class="text-xs text-gray-500">Mahasiswa: <span class="text-gray-400">{{ $project->selectedStudent?->user?->name ?? '—' }}</span></p>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">Budget: Rp {{ number_format($project->agreed_budget, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex gap-2 flex-shrink-0">
                        <form action="{{ route('admin.resolve-dispute', [$project->id, 'release']) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-success px-4 py-2 rounded-xl text-xs font-bold"
                                onclick="return confirm('Cairkan dana ke mahasiswa?')">
                                <i class="fas fa-check mr-1"></i>Cairkan ke Mahasiswa
                            </button>
                        </form>
                        <form action="{{ route('admin.resolve-dispute', [$project->id, 'refund']) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-red-400 border border-red-500/30 hover:bg-red-500/10 transition-colors"
                                onclick="return confirm('Kembalikan dana ke client?')">
                                <i class="fas fa-undo mr-1"></i>Refund ke Client
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Recent Payments --}}
    <div class="card overflow-hidden">
        <div class="p-5 border-b border-white/5">
            <h2 class="text-sm font-bold text-white">Transaksi Terkini</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Proyek</th>
                        <th class="text-left">Client</th>
                        <th class="text-left">Jumlah</th>
                        <th class="text-left">Platform Fee</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPayments as $payment)
                    <tr>
                        <td class="text-sm text-white">{{ Str::limit($payment->project?->title, 30) }}</td>
                        <td class="text-sm text-gray-300">{{ $payment->client?->user?->name }}</td>
                        <td class="text-sm font-bold text-white">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td class="text-sm text-accent-400">Rp {{ number_format($payment->platform_fee, 0, ',', '.') }}</td>
                        <td>
                            @php
                                $badge = [
                                    'pending' => ['class' => 'badge-waiting', 'label' => 'Pending'],
                                    'held' => ['class' => 'badge-progress', 'label' => 'Ditahan'],
                                    'released' => ['class' => 'badge-completed', 'label' => 'Cair'],
                                    'refunded' => ['class' => 'badge-revision', 'label' => 'Refund'],
                                ][$payment->status] ?? ['class' => 'badge-open', 'label' => $payment->status];
                            @endphp
                            <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                        </td>
                        <td class="text-xs text-gray-500">{{ $payment->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-8 text-gray-600">Tidak ada transaksi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
