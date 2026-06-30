@extends('layouts.app')

@section('title', 'KRS & Skills')
@section('page-title', 'KRS & Skills AI')
@section('page-subtitle', 'Upload KRS untuk deteksi skill otomatis dengan AI')

@section('content')
<div class="space-y-6 animate-fade-in">

    {{-- Status Banner --}}
    @php
        $krsStatus = $student?->krs_status ?? 'not_uploaded';
        $statusConfig = [
            'not_uploaded' => ['bg' => 'bg-gray-800/50 border-gray-700/50', 'icon' => 'fa-cloud-upload-alt', 'iconColor' => 'text-gray-500', 'title' => 'KRS Belum Diupload', 'desc' => 'Upload KRS Anda agar AI dapat mendeteksi skill secara otomatis'],
            'uploaded' => ['bg' => 'bg-blue-900/30 border-blue-500/30', 'icon' => 'fa-clock', 'iconColor' => 'text-blue-400', 'title' => 'KRS Sedang Diproses', 'desc' => 'AI sedang menganalisis KRS Anda...'],
            'parsed' => ['bg' => 'bg-accent-900/20 border-accent-500/30', 'icon' => 'fa-check-circle', 'iconColor' => 'text-accent-400', 'title' => 'KRS Berhasil Diproses', 'desc' => 'AI berhasil mendeteksi skill dari KRS Anda'],
            'failed' => ['bg' => 'bg-red-900/30 border-red-500/30', 'icon' => 'fa-exclamation-circle', 'iconColor' => 'text-red-400', 'title' => 'Proses KRS Gagal', 'desc' => 'Gagal memproses KRS. Silakan upload ulang.'],
        ][$krsStatus] ?? ['bg' => 'bg-gray-800/50 border-gray-700/50', 'icon' => 'fa-question', 'iconColor' => 'text-gray-500', 'title' => 'Status Tidak Diketahui', 'desc' => ''];
    @endphp

    <div class="p-5 {{ $statusConfig['bg'] }} border rounded-xl flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center flex-shrink-0">
            <i class="fas {{ $statusConfig['icon'] }} text-xl {{ $statusConfig['iconColor'] }}"></i>
        </div>
        <div class="flex-1">
            <p class="font-semibold text-white">{{ $statusConfig['title'] }}</p>
            <p class="text-sm text-gray-400 mt-0.5">{{ $statusConfig['desc'] }}</p>
        </div>
        @if($krsStatus === 'parsed')
        <div class="text-right flex-shrink-0">
            <div class="text-2xl font-black text-accent-400">{{ $student?->skills->count() ?? 0 }}</div>
            <div class="text-xs text-gray-500">Skills</div>
        </div>
        @endif
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Upload Form --}}
        <div class="card p-6">
            <h2 class="text-base font-bold text-white mb-2 flex items-center gap-2">
                <i class="fas fa-upload text-primary-400"></i> Upload KRS
            </h2>
            <p class="text-sm text-gray-500 mb-5">AI akan menganalisis KRS Anda dan mengekstrak mata kuliah yang sudah ditempuh untuk menghasilkan peta skill.</p>

            <form action="{{ route('student.krs.upload') }}" method="POST" enctype="multipart/form-data" id="krsForm">
                @csrf
                <div id="dropzone"
                    class="border-2 border-dashed border-white/10 hover:border-primary-500/50 rounded-xl p-8 text-center cursor-pointer transition-all relative"
                    onclick="document.getElementById('krsFile').click()">
                    <input type="file" id="krsFile" name="krs_file" accept=".pdf,.jpg,.jpeg,.png" class="hidden" onchange="updateDropzone(this)">
                    <div id="dropzoneContent">
                        <div class="w-16 h-16 bg-primary-500/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-file-pdf text-3xl text-primary-400"></i>
                        </div>
                        <p class="text-sm font-semibold text-gray-300 mb-1">Drag & drop atau klik untuk upload</p>
                        <p class="text-xs text-gray-600">PDF, JPG, PNG · Maks. 10MB</p>
                    </div>
                    <div id="fileSelected" class="hidden">
                        <div class="w-16 h-16 bg-accent-500/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-file-check text-3xl text-accent-400"></i>
                        </div>
                        <p class="text-sm font-semibold text-white" id="fileName"></p>
                        <p class="text-xs text-gray-500 mt-1">Klik tombol upload untuk melanjutkan</p>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-white/3 rounded-xl border border-white/5">
                    <p class="text-xs font-semibold text-gray-400 mb-2 flex items-center gap-2">
                        <i class="fas fa-robot text-primary-400"></i> AI Detection Preview
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Python', 'Machine Learning', 'Data Analysis', 'Web Dev', 'SQL', 'Statistics'] as $skill)
                        <span class="text-xs px-2 py-1 bg-primary-500/10 text-primary-400 rounded-full border border-primary-500/20 opacity-40">{{ $skill }}</span>
                        @endforeach
                        <span class="text-xs text-gray-600 self-center">+ lebih banyak setelah upload</span>
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full mt-4 py-3 rounded-xl text-sm font-semibold" id="uploadBtn">
                    <i class="fas fa-brain mr-2"></i>Proses KRS dengan AI
                </button>
            </form>

            @if($krsStatus === 'parsed')
            <form action="{{ route('student.krs.reparse') }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="btn-outline w-full py-2.5 rounded-xl text-sm font-medium">
                    <i class="fas fa-redo mr-2"></i>Parse Ulang KRS
                </button>
            </form>
            @endif
        </div>

        {{-- Detected Skills --}}
        <div class="card p-6">
            <h2 class="text-base font-bold text-white mb-5 flex items-center gap-2">
                <i class="fas fa-brain text-purple-400"></i> Skill Terdeteksi
            </h2>

            @if(!$student?->skills || $student->skills->isEmpty())
            <div class="text-center py-10">
                <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-brain text-2xl text-gray-600"></i>
                </div>
                <p class="text-gray-500 text-sm">Belum ada skill terdeteksi</p>
                <p class="text-gray-600 text-xs mt-1">Upload KRS untuk memulai</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach($student->skills->sortByDesc('proficiency_level') as $skill)
                <div class="p-3 bg-white/3 rounded-xl border border-white/5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-white">{{ $skill->skill_name }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full
                            @if($skill->proficiency_level >= 4) bg-accent-500/20 text-accent-400
                            @elseif($skill->proficiency_level >= 2) bg-yellow-500/20 text-yellow-400
                            @else bg-gray-500/20 text-gray-400 @endif">
                            @if($skill->proficiency_level >= 4) Expert
                            @elseif($skill->proficiency_level >= 2) Menengah
                            @else Pemula @endif
                        </span>
                    </div>
                    <div class="progress-bar h-1.5">
                        <div class="progress-fill
                            @if($skill->proficiency_level >= 4) bg-gradient-to-r from-accent-500 to-accent-400
                            @elseif($skill->proficiency_level >= 2) bg-gradient-to-r from-yellow-600 to-yellow-400
                            @else bg-gradient-to-r from-gray-600 to-gray-500 @endif"
                            style="width: {{ min(($skill->proficiency_level / 5) * 100, 100) }}%">
                        </div>
                    </div>
                    @if($skill->source_courses && count($skill->source_courses) > 0)
                    <p class="text-[10px] text-gray-600 mt-1.5">
                        Dari: {{ implode(', ', array_slice($skill->source_courses, 0, 2)) }}
                    </p>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- How It Works --}}
    <div class="card p-6">
        <h2 class="text-base font-bold text-white mb-5">Bagaimana AI Bekerja?</h2>
        <div class="grid sm:grid-cols-3 gap-4">
            @foreach([
                ['step' => '1', 'icon' => 'fa-upload', 'title' => 'Upload KRS', 'desc' => 'Upload file KRS dalam format PDF atau gambar', 'color' => 'primary'],
                ['step' => '2', 'icon' => 'fa-robot', 'title' => 'AI Analisis', 'desc' => 'AI mengekstrak mata kuliah dan memetakan ke skill teknologi', 'color' => 'purple'],
                ['step' => '3', 'icon' => 'fa-bolt', 'title' => 'Auto-Matching', 'desc' => 'Sistem mencarikan proyek yang paling sesuai dengan skill Anda', 'color' => 'accent'],
            ] as $step)
            <div class="flex gap-4">
                <div class="w-10 h-10 bg-{{ $step['color'] }}-500/20 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="fas {{ $step['icon'] }} text-{{ $step['color'] }}-400"></i>
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

@push('scripts')
<script>
function updateDropzone(input) {
    if (input.files.length > 0) {
        document.getElementById('dropzoneContent').classList.add('hidden');
        document.getElementById('fileSelected').classList.remove('hidden');
        document.getElementById('fileName').textContent = input.files[0].name;
        document.getElementById('dropzone').classList.add('border-primary-500/50', 'bg-primary-500/5');
    }
}

document.getElementById('krsForm').addEventListener('submit', function() {
    const btn = document.getElementById('uploadBtn');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
    btn.disabled = true;
});
</script>
@endpush
