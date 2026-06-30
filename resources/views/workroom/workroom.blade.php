@extends('layouts.app')

@section('title', 'Workroom - ' . $project->title)
@section('page-title', 'Workroom')
@section('page-subtitle', $project->title)

@section('content')
<div class="grid lg:grid-cols-3 gap-6 h-full animate-fade-in">

    {{-- Left: Chat + Progress --}}
    <div class="lg:col-span-2 flex flex-col gap-5">

        {{-- Progress Card --}}
        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h2 class="text-sm font-bold text-white">Progress Pengerjaan</h2>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $workroom->status_label ?? ucfirst($workroom->status) }}</p>
                </div>
                <div class="text-2xl font-black text-primary-400">{{ $workroom->progress_percentage }}%</div>
            </div>
            <div class="progress-bar h-3">
                <div class="progress-fill bg-gradient-to-r from-primary-600 via-primary-400 to-accent-500"
                    style="width: {{ $workroom->progress_percentage }}%"></div>
            </div>
            @if($user->isMahasiswa() && in_array($workroom->status, ['active', 'in_progress']))
            <form action="{{ route('workroom.progress', $workroom->id) }}" method="POST" class="mt-4 flex gap-3">
                @csrf
                <input type="range" name="progress" min="0" max="100"
                    value="{{ $workroom->progress_percentage }}"
                    class="flex-1 accent-primary-500"
                    oninput="document.getElementById('progressValue').textContent = this.value + '%'">
                <span id="progressValue" class="text-sm font-bold text-primary-400 w-12 text-right">{{ $workroom->progress_percentage }}%</span>
                <button type="submit" class="btn-primary px-4 py-1.5 rounded-xl text-xs font-semibold flex-shrink-0">Update</button>
            </form>
            @endif
        </div>

        {{-- Chat Room --}}
        <div class="card flex flex-col" style="height: 500px;">
            <div class="px-5 py-4 border-b border-white/5 flex items-center gap-3">
                <div class="w-2 h-2 bg-accent-400 rounded-full animate-pulse"></div>
                <h2 class="text-sm font-bold text-white">Komunikasi Proyek</h2>
            </div>

            {{-- Messages --}}
            <div class="flex-1 overflow-y-auto p-5 space-y-4" id="chatMessages">
                @if(empty($workroom->messages))
                <div class="text-center py-10 text-gray-600 text-sm">
                    <i class="fas fa-comments text-2xl mb-2 block"></i>
                    Belum ada pesan. Mulai komunikasi!
                </div>
                @else
                @foreach($workroom->messages as $msg)
                @php $isMe = $msg['user_id'] === auth()->id(); @endphp

                @if($msg['type'] === 'system' || $msg['type'] === 'revision' || $msg['type'] === 'approved')
                {{-- System Message --}}
                <div class="flex justify-center">
                    <div class="px-4 py-2 bg-white/5 rounded-full text-xs text-gray-400 text-center max-w-sm">
                        {{ $msg['message'] }}
                    </div>
                </div>
                @else
                {{-- User Message --}}
                <div class="flex gap-3 {{ $isMe ? 'flex-row-reverse' : '' }}">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold
                        {{ $msg['role'] === 'mahasiswa' ? 'bg-primary-500/30 text-primary-300' : 'bg-accent-500/30 text-accent-300' }}">
                        {{ strtoupper(substr($msg['user_name'], 0, 1)) }}
                    </div>
                    <div class="{{ $isMe ? 'items-end' : 'items-start' }} flex flex-col gap-1 max-w-xs lg:max-w-md">
                        <span class="text-[10px] text-gray-600 {{ $isMe ? 'text-right' : '' }}">{{ $msg['user_name'] }}</span>
                        <div class="px-4 py-2.5 rounded-2xl {{ $isMe ? 'bg-primary-500/25 border border-primary-500/30 text-primary-100 rounded-tr-sm' : 'bg-white/8 border border-white/8 text-gray-200 rounded-tl-sm' }}">
                            <p class="text-sm leading-relaxed">{{ $msg['message'] }}</p>
                        </div>
                        <span class="text-[10px] text-gray-700">{{ \Carbon\Carbon::parse($msg['created_at'])->format('H:i') }}</span>
                    </div>
                </div>
                @endif
                @endforeach
                @endif
            </div>

            {{-- Chat Input --}}
            @if(!in_array($workroom->status, ['approved', 'completed', 'disputed']))
            <div class="p-4 border-t border-white/5">
                <form action="{{ route('workroom.message', $workroom->id) }}" method="POST" class="flex gap-3">
                    @csrf
                    <input type="text" name="message" required
                        placeholder="Tulis pesan..."
                        class="input-dark flex-1 text-sm py-2.5">
                    <button type="submit" class="btn-primary px-5 py-2.5 rounded-xl text-sm font-semibold flex-shrink-0">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    {{-- Right Sidebar --}}
    <div class="space-y-5">
        {{-- Project Info --}}
        <div class="card p-5">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Info Proyek</h3>
            <div class="space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">Budget</span>
                    <span class="text-sm font-bold text-white">Rp {{ number_format($project->agreed_budget, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">Deadline</span>
                    <span class="text-sm text-gray-300">{{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">Revisi</span>
                    <span class="text-sm text-gray-300">{{ $project->revision_count }}/{{ $project->max_revisions }}x</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">Status</span>
                    @php
                        $badge = [
                            'active' => ['class' => 'badge-progress', 'label' => 'Aktif'],
                            'submitted' => ['class' => 'badge-review', 'label' => 'Diserahkan'],
                            'revision' => ['class' => 'badge-revision', 'label' => 'Revisi'],
                            'approved' => ['class' => 'badge-completed', 'label' => 'Disetujui'],
                            'disputed' => ['class' => 'badge-disputed', 'label' => 'Sengketa'],
                        ][$workroom->status] ?? ['class' => 'badge-open', 'label' => $workroom->status];
                    @endphp
                    <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                </div>
            </div>
        </div>

        {{-- Deliverable Upload (Student) --}}
        @if($user->isMahasiswa() && !in_array($workroom->status, ['approved', 'completed']))
        <div class="card p-5">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Upload Deliverable</h3>
            <form action="{{ route('workroom.upload', $workroom->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <input type="file" name="deliverable_file" required id="delivFile" class="hidden"
                        onchange="document.getElementById('delivName').textContent = this.files[0].name">
                    <div onclick="document.getElementById('delivFile').click()"
                        class="border border-dashed border-white/15 hover:border-primary-500/40 rounded-xl p-4 text-center cursor-pointer transition-all">
                        <i class="fas fa-cloud-upload-alt text-xl text-gray-600 block mb-2"></i>
                        <p id="delivName" class="text-xs text-gray-500">Klik untuk pilih file (Max 20MB)</p>
                    </div>
                </div>
                <textarea name="notes" rows="2" placeholder="Catatan deliverable..." class="input-dark w-full text-xs mb-3 resize-none"></textarea>
                <button type="submit" class="btn-success w-full py-2.5 rounded-xl text-sm font-semibold">
                    <i class="fas fa-upload mr-2"></i>Submit Deliverable
                </button>
            </form>
        </div>
        @endif

        {{-- Deliverables List --}}
        @if($workroom->deliverables && count($workroom->deliverables) > 0)
        <div class="card p-5">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Deliverables</h3>
            <div class="space-y-2">
                @foreach($workroom->deliverables as $del)
                <div class="flex items-center gap-3 p-3 bg-white/3 rounded-xl border border-white/5">
                    <div class="w-8 h-8 bg-primary-500/15 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-file text-primary-400 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-white truncate">{{ $del['filename'] }}</p>
                        <p class="text-[10px] text-gray-600">{{ \Carbon\Carbon::parse($del['uploaded_at'])->format('d M, H:i') }}</p>
                    </div>
                    <a href="{{ Storage::url($del['path']) }}" target="_blank" class="text-xs text-primary-400 hover:text-primary-300 flex-shrink-0">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Client Actions --}}
        @if($user->isClient())
        <div class="card p-5">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Tindakan Client</h3>
            <div class="space-y-2">
                @if($workroom->status === 'submitted')
                {{-- Approve Work --}}
                <form action="{{ route('workroom.approve', $workroom->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-success w-full py-2.5 rounded-xl text-sm font-semibold"
                        onclick="return confirm('Setujui pekerjaan ini dan cairkan dana?')">
                        <i class="fas fa-check mr-2"></i>Setujui Pekerjaan
                    </button>
                </form>

                {{-- Request Revision --}}
                @if($project->revision_count < $project->max_revisions)
                <button onclick="document.getElementById('revisionModal').classList.remove('hidden')"
                    class="btn-outline w-full py-2.5 rounded-xl text-sm font-semibold">
                    <i class="fas fa-redo mr-2"></i>Minta Revisi ({{ $project->max_revisions - $project->revision_count }}x tersisa)
                </button>
                @endif
                @endif

                {{-- Dispute --}}
                @if(!in_array($workroom->status, ['approved', 'disputed', 'completed']))
                <form action="{{ route('workroom.dispute', $workroom->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-2 rounded-xl text-xs text-gray-600 hover:text-red-400 transition-colors"
                        onclick="return confirm('Ajukan sengketa untuk proyek ini?')">
                        <i class="fas fa-flag mr-1"></i>Ajukan Sengketa
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endif

        {{-- Revisions --}}
        @if($workroom->revisions->isNotEmpty())
        <div class="card p-5">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Riwayat Revisi</h3>
            <div class="space-y-3">
                @foreach($workroom->revisions as $revision)
                <div class="p-3 bg-white/3 rounded-xl border border-white/5">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold text-white">Revisi #{{ $revision->revision_number }}</span>
                        <span class="badge
                            {{ $revision->status === 'pending' ? 'badge-waiting' : ($revision->status === 'completed' ? 'badge-completed' : 'badge-progress') }}">
                            {{ ucfirst($revision->status) }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-400">{{ Str::limit($revision->feedback, 80) }}</p>
                    <p class="text-[10px] text-gray-600 mt-1">{{ $revision->requested_at ? \Carbon\Carbon::parse($revision->requested_at)->format('d M Y') : '' }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Revision Modal --}}
<div id="revisionModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md p-6">
        <h3 class="text-base font-bold text-white mb-4">Permintaan Revisi</h3>
        <form action="{{ route('workroom.revision', $workroom->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-400 mb-2">Feedback Detail <span class="text-red-400">*</span></label>
                <textarea name="feedback" required rows="4"
                    placeholder="Jelaskan apa yang perlu diperbaiki secara detail..."
                    class="input-dark w-full text-sm resize-none"></textarea>
            </div>
            <div class="mb-5">
                <label class="block text-xs font-semibold text-gray-400 mb-2">Perubahan Spesifik (pisahkan dengan koma)</label>
                <input type="text" name="specific_changes"
                    placeholder="Contoh: Ubah warna header, Perbaiki navigasi, ..."
                    class="input-dark w-full text-sm">
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('revisionModal').classList.add('hidden')"
                    class="btn-outline flex-1 py-2.5 rounded-xl text-sm">Batal</button>
                <button type="submit" class="btn-primary flex-1 py-2.5 rounded-xl text-sm font-semibold">
                    Kirim Permintaan Revisi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto scroll chat to bottom
const chatEl = document.getElementById('chatMessages');
if (chatEl) chatEl.scrollTop = chatEl.scrollHeight;
</script>
@endpush
