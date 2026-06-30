@extends('layouts.app')

@section('title', 'Proyek Saya')
@section('page-title', 'Proyek Saya')
@section('page-subtitle', 'Kelola semua proyek yang Anda buat')

@section('content')
<div class="space-y-6 animate-fade-in">

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'Total Dibuat', 'value' => $stats['total_posted'], 'icon' => 'fa-folder-plus', 'color' => 'primary'],
            ['label' => 'Selesai', 'value' => $stats['total_completed'], 'icon' => 'fa-check-double', 'color' => 'accent'],
            ['label' => 'Aktif', 'value' => $stats['active'], 'icon' => 'fa-spinner', 'color' => 'yellow'],
            ['label' => 'Total Bayar', 'value' => 'Rp ' . number_format($stats['total_spent'], 0, ',', '.'), 'icon' => 'fa-wallet', 'color' => 'green'],
        ] as $stat)
        <div class="card p-5">
            <div class="w-10 h-10 bg-{{ $stat['color'] }}-500/20 rounded-xl flex items-center justify-center mb-3">
                <i class="fas {{ $stat['icon'] }} text-{{ $stat['color'] }}-400"></i>
            </div>
            <div class="text-xl font-black text-white">{{ $stat['value'] }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ $stat['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Projects Table --}}
    <div class="card overflow-hidden">
        <div class="p-6 border-b border-white/5 flex items-center justify-between">
            <h2 class="text-base font-bold text-white">Semua Proyek</h2>
            <a href="{{ route('client.create-project') }}" class="btn-primary px-4 py-2 rounded-xl text-sm font-semibold">
                <i class="fas fa-plus mr-2"></i>Proyek Baru
            </a>
        </div>

        @if($projects->isEmpty())
        <div class="text-center py-16">
            <div class="w-20 h-20 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-5">
                <i class="fas fa-folder-open text-3xl text-gray-600"></i>
            </div>
            <p class="text-lg font-semibold text-gray-400">Belum ada proyek</p>
            <a href="{{ route('client.create-project') }}" class="btn-primary inline-block mt-4 px-5 py-2.5 rounded-xl text-sm font-semibold">
                Buat Proyek Pertama
            </a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Proyek</th>
                        <th class="text-left">Mahasiswa</th>
                        <th class="text-left">Budget</th>
                        <th class="text-left">Deadline</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projects as $project)
                    <tr>
                        <td>
                            <p class="text-sm font-semibold text-white">{{ Str::limit($project->title, 35) }}</p>
                            <p class="text-xs text-gray-600 mt-0.5">{{ $project->category }}</p>
                        </td>
                        <td>
                            @if($project->selectedStudent)
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-primary-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-primary-400">{{ strtoupper(substr($project->selectedStudent->user->name, 0, 1)) }}</span>
                                </div>
                                <span class="text-sm text-gray-300">{{ $project->selectedStudent->user->name }}</span>
                            </div>
                            @else
                            <span class="text-xs text-gray-600">Belum dipilih</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-sm font-semibold text-white">Rp {{ number_format($project->agreed_budget ?? $project->budget_max, 0, ',', '.') }}</span>
                        </td>
                        <td>
                            <span class="text-sm text-gray-300">{{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}</span>
                        </td>
                        <td>
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
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                @if($project->status === 'open')
                                <a href="{{ route('client.candidates', $project->id) }}" class="btn-primary px-3 py-1.5 rounded-lg text-xs">
                                    Kandidat
                                </a>
                                @elseif($project->status === 'waiting_payment')
                                <a href="{{ route('payment.escrow', $project->id) }}" class="btn-success px-3 py-1.5 rounded-lg text-xs font-semibold">
                                    Bayar
                                </a>
                                @elseif(in_array($project->status, ['in_progress', 'in_review', 'revision']))
                                <a href="{{ route('workroom.show', $project->id) }}" class="btn-primary px-3 py-1.5 rounded-lg text-xs">
                                    Workroom
                                </a>
                                @endif
                                <a href="{{ route('client.project-detail', $project->id) }}" class="btn-outline px-3 py-1.5 rounded-lg text-xs">
                                    Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($projects->hasPages())
        <div class="p-4 border-t border-white/5">
            {{ $projects->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
