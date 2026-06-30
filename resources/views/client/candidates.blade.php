@extends('layouts.app')

@section('title', 'Kandidat AI')
@section('page-title', 'Kandidat AI')
@section('page-subtitle', 'Mahasiswa terbaik yang dipilih AI untuk proyek Anda')

@section('content')
<div class="space-y-6 animate-fade-in">

    {{-- Project Summary --}}
    <div class="card p-5 flex items-center gap-4">
        <div class="w-10 h-10 bg-primary-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-briefcase text-primary-400"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-white truncate">{{ $project->title }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ $project->category }} · Budget: Rp {{ number_format($project->budget_max, 0, ',', '.') }} · Deadline: {{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}</p>
        </div>
        <div class="flex-shrink-0">
            <span class="badge badge-open">{{ $matchings->count() }} Kandidat Ditemukan</span>
        </div>
    </div>

    {{-- AI Info --}}
    <div class="p-4 bg-primary-950/50 border border-primary-800/30 rounded-xl flex items-start gap-3">
        <div class="w-8 h-8 bg-primary-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fas fa-robot text-primary-400 text-sm"></i>
        </div>
        <div>
            <p class="text-sm font-semibold text-primary-300">AI Multi-Criteria Scoring</p>
            <p class="text-xs text-primary-400/60 mt-0.5">Kandidat diurutkan berdasarkan skor AI yang mempertimbangkan kesesuaian skill dari KRS, portfolio, rating rata-rata, dan riwayat proyek yang berhasil diselesaikan.</p>
        </div>
    </div>

    {{-- Candidates Grid --}}
    @if($matchings->isEmpty())
    <div class="card p-16 text-center">
        <div class="w-20 h-20 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-5">
            <i class="fas fa-search text-3xl text-gray-600"></i>
        </div>
        <p class="text-lg font-semibold text-gray-400">Tidak ada kandidat ditemukan</p>
        <p class="text-sm text-gray-600 mt-1">AI sedang mencari mahasiswa yang sesuai dengan proyek Anda. Coba lagi nanti.</p>
    </div>
    @else
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($matchings as $index => $matching)
        @php $student = $matching->student; @endphp
        <div class="card overflow-hidden group hover:border-primary-500/30 transition-all border border-white/5 {{ $index === 0 ? 'border-yellow-500/30 ring-1 ring-yellow-500/20' : '' }}">
            {{-- Top Candidate Badge --}}
            @if($index === 0)
            <div class="bg-gradient-to-r from-yellow-600/40 to-amber-600/20 px-4 py-2 flex items-center gap-2 border-b border-yellow-500/20">
                <i class="fas fa-crown text-yellow-400 text-xs"></i>
                <span class="text-xs font-bold text-yellow-300">Top AI Pick</span>
            </div>
            @endif

            <div class="p-5">
                {{-- Student Header --}}
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                        <span class="text-xl font-black text-white">{{ strtoupper(substr($student->user->name, 0, 1)) }}</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-white truncate">{{ $student->user->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $student->university }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="text-xl font-black text-primary-400">{{ round($matching->match_score) }}%</div>
                        <div class="text-[10px] text-gray-600">AI Score</div>
                    </div>
                </div>

                {{-- AI Score Bar --}}
                <div class="progress-bar h-2 mb-4">
                    <div class="progress-fill bg-gradient-to-r from-primary-600 to-accent-500"
                        style="width: {{ $matching->match_score }}%"></div>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-3 gap-2 mb-4">
                    <div class="text-center p-2 bg-white/3 rounded-lg">
                        <div class="text-sm font-bold text-white">{{ $student->total_projects }}</div>
                        <div class="text-[10px] text-gray-600">Proyek</div>
                    </div>
                    <div class="text-center p-2 bg-white/3 rounded-lg">
                        <div class="text-sm font-bold text-white">{{ number_format($student->average_rating, 1) }}</div>
                        <div class="text-[10px] text-gray-600">Rating</div>
                    </div>
                    <div class="text-center p-2 bg-white/3 rounded-lg">
                        <div class="text-sm font-bold text-white">{{ $student->skills->count() }}</div>
                        <div class="text-[10px] text-gray-600">Skills</div>
                    </div>
                </div>

                {{-- Skills --}}
                @if($student->skills->isNotEmpty())
                <div class="flex flex-wrap gap-1 mb-4">
                    @foreach($student->skills->take(4) as $skill)
                    <span class="text-[10px] px-2 py-0.5 bg-primary-500/10 text-primary-400 rounded-full border border-primary-500/20">{{ $skill->skill_name }}</span>
                    @endforeach
                    @if($student->skills->count() > 4)
                    <span class="text-[10px] px-2 py-0.5 bg-white/5 text-gray-500 rounded-full">+{{ $student->skills->count() - 4 }}</span>
                    @endif
                </div>
                @endif

                {{-- AI Reasoning --}}
                @if($matching->match_reason)
                <div class="p-2.5 bg-white/3 rounded-lg mb-4 border border-white/5">
                    <p class="text-[10px] text-gray-500 leading-relaxed">💡 {{ Str::limit($matching->match_reason, 100) }}</p>
                </div>
                @endif

                {{-- Select Form --}}
                @if($project->status === 'open')
                <form action="{{ route('client.select-candidate', $project->id) }}" method="POST" class="space-y-2">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[10px] text-gray-500">Rp</span>
                            <input type="number" name="agreed_budget"
                                value="{{ $project->budget_max }}"
                                min="{{ $project->budget_min }}"
                                max="{{ $project->budget_max }}"
                                class="input-dark w-full text-xs pl-7 py-2">
                        </div>
                        <button type="submit" class="btn-primary px-4 py-2 rounded-xl text-xs font-bold flex-shrink-0">
                            Pilih
                        </button>
                    </div>
                </form>
                @else
                <div class="text-center py-2 text-xs text-gray-600">
                    @if($project->selected_student_id === $student->id)
                    <span class="text-accent-400"><i class="fas fa-check-circle mr-1"></i>Terpilih</span>
                    @else
                    <span>Tidak tersedia</span>
                    @endif
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
