@extends('layouts.app')

@section('title', 'Proyek Saya')
@section('page-title', 'Proyek Saya')
@section('page-subtitle', 'Riwayat dan status semua proyek yang Anda kerjakan')

@section('content')
<div class="space-y-6 animate-fade-in">

    {{-- Projects Table --}}
    <div class="card overflow-hidden">
        <div class="p-6 border-b border-white/5 flex items-center justify-between">
            <h2 class="text-base font-bold text-white">Semua Proyek</h2>
            <span class="badge badge-progress">{{ $projects->total() }} Total</span>
        </div>

        @if($projects->isEmpty())
        <div class="text-center py-16">
            <div class="w-20 h-20 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-5">
                <i class="fas fa-briefcase text-3xl text-gray-600"></i>
            </div>
            <p class="text-lg font-semibold text-gray-400">Belum ada proyek</p>
            <p class="text-sm text-gray-600 mt-1 mb-5">Proyek yang Anda kerjakan akan muncul di sini</p>
            <a href="{{ route('student.dashboard') }}" class="btn-primary inline-block px-5 py-2.5 rounded-xl text-sm font-semibold">
                Lihat Rekomendasi AI →
            </a>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Proyek</th>
                        <th class="text-left">Client</th>
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
                            <p class="text-sm font-semibold text-white">{{ Str::limit($project->title, 40) }}</p>
                            <p class="text-xs text-gray-600 mt-0.5">{{ $project->category }}</p>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-accent-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-accent-400">{{ strtoupper(substr($project->client->user->name, 0, 1)) }}</span>
                                </div>
                                <span class="text-sm text-gray-300">{{ $project->client->user->name }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="text-sm font-semibold text-white">Rp {{ number_format($project->agreed_budget ?? $project->budget_max, 0, ',', '.') }}</span>
                        </td>
                        <td>
                            @php
                                $deadline = \Carbon\Carbon::parse($project->deadline);
                                $isOverdue = $deadline->isPast() && !in_array($project->status, ['completed', 'cancelled']);
                            @endphp
                            <span class="text-sm {{ $isOverdue ? 'text-red-400' : 'text-gray-300' }}">
                                {{ $deadline->format('d M Y') }}
                            </span>
                            @if($isOverdue)
                            <p class="text-xs text-red-500">Melewati deadline</p>
                            @endif
                        </td>
                        <td>
                            @php
                                $badges = [
                                    'open' => ['class' => 'badge-open', 'label' => 'Open'],
                                    'waiting_payment' => ['class' => 'badge-waiting', 'label' => 'Menunggu Bayar'],
                                    'in_progress' => ['class' => 'badge-progress', 'label' => 'In Progress'],
                                    'in_review' => ['class' => 'badge-review', 'label' => 'Review'],
                                    'revision' => ['class' => 'badge-revision', 'label' => 'Revisi'],
                                    'completed' => ['class' => 'badge-completed', 'label' => 'Selesai'],
                                    'cancelled' => ['class' => 'badge-revision', 'label' => 'Dibatalkan'],
                                    'disputed' => ['class' => 'badge-disputed', 'label' => 'Sengketa'],
                                ];
                                $badge = $badges[$project->status] ?? ['class' => 'badge-open', 'label' => $project->status];
                            @endphp
                            <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                        </td>
                        <td>
                            @if(in_array($project->status, ['in_progress', 'in_review', 'revision']))
                            <a href="{{ route('workroom.show', $project->id) }}"
                                class="btn-primary px-3 py-1.5 rounded-lg text-xs font-semibold inline-block">
                                Buka Workroom
                            </a>
                            @elseif($project->status === 'completed')
                            <span class="text-xs text-accent-400 flex items-center gap-1">
                                <i class="fas fa-check-circle"></i> Selesai
                            </span>
                            @else
                            <span class="text-xs text-gray-600">—</span>
                            @endif
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
