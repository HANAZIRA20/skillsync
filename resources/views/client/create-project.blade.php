@extends('layouts.app')

@section('title', 'Buat Proyek Baru')
@section('page-title', 'Buat Proyek Baru')
@section('page-subtitle', 'Deskripsikan proyek Anda dan AI akan mencarikan kandidat terbaik')

@section('content')
<div class="max-w-3xl animate-fade-in">

    <form action="{{ route('client.store-project') }}" method="POST" id="projectForm">
        @csrf

        {{-- Step Indicator --}}
        <div class="card p-5 mb-6">
            <div class="flex items-center gap-0">
                @foreach([['1', 'Detail Proyek'], ['2', 'Skill & Budget'], ['3', 'AI Matching']] as $i => $step)
                <div class="flex items-center {{ $i > 0 ? 'flex-1' : '' }}">
                    @if($i > 0)
                    <div class="flex-1 h-0.5 bg-white/10 mx-2"></div>
                    @endif
                    <div class="flex flex-col items-center gap-1">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                            {{ $i === 0 ? 'bg-primary-500 text-white' : 'bg-white/10 text-gray-500' }}">
                            {{ $step[0] }}
                        </div>
                        <span class="text-[10px] {{ $i === 0 ? 'text-primary-400' : 'text-gray-600' }} whitespace-nowrap">{{ $step[1] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="card p-6 mb-4">
            <h2 class="text-base font-bold text-white mb-5 flex items-center gap-2">
                <i class="fas fa-info-circle text-primary-400"></i> Informasi Proyek
            </h2>

            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-2">Judul Proyek <span class="text-red-400">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                        placeholder="Contoh: Pembuatan Website E-commerce UMKM..."
                        class="input-dark w-full text-sm">
                    @error('title')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-2">Kategori <span class="text-red-400">*</span></label>
                    <select name="category" required class="input-dark w-full text-sm">
                        <option value="">— Pilih Kategori —</option>
                        @foreach(['Web Development', 'Mobile Development', 'Data Science', 'UI/UX Design', 'Machine Learning', 'Backend API', 'Frontend Development', 'Database', 'DevOps', 'Cybersecurity', 'Digital Marketing', 'Content Writing', 'Graphic Design', 'Lainnya'] as $cat)
                        <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    @error('category')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-2">Deskripsi Proyek <span class="text-red-400">*</span> <span class="text-gray-600 font-normal">(Min. 50 karakter)</span></label>
                    <textarea name="description" required rows="5"
                        placeholder="Jelaskan detail proyek Anda: tujuan, fitur yang dibutuhkan, hasil yang diharapkan, teknologi yang boleh digunakan, dll..."
                        class="input-dark w-full text-sm resize-none">{{ old('description') }}</textarea>
                    @error('description')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="card p-6 mb-4">
            <h2 class="text-base font-bold text-white mb-5 flex items-center gap-2">
                <i class="fas fa-code text-purple-400"></i> Skill & Budget
            </h2>

            <div class="space-y-5">
                {{-- Required Skills --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-2">Skill yang Dibutuhkan <span class="text-red-400">*</span></label>
                    <div class="mb-2">
                        <input type="text" id="skillInput"
                            placeholder="Ketik skill lalu tekan Enter atau tombol Tambah..."
                            class="input-dark w-full text-sm">
                    </div>
                    <div id="skillTags" class="flex flex-wrap gap-2 min-h-[36px] p-2 bg-white/3 rounded-xl border border-white/5"></div>
                    <div id="skillInputs"></div>
                    @error('required_skills')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror

                    <div class="flex flex-wrap gap-1.5 mt-3">
                        <p class="text-xs text-gray-600 w-full">Saran cepat:</p>
                        @foreach(['JavaScript', 'Python', 'Laravel', 'React', 'Vue.js', 'MySQL', 'UI/UX', 'Flutter', 'Node.js', 'Docker'] as $sugg)
                        <button type="button" onclick="addSkill('{{ $sugg }}')"
                            class="text-xs px-2 py-1 bg-white/5 text-gray-400 rounded-full border border-white/10 hover:border-primary-500/40 hover:text-primary-400 transition-all">
                            + {{ $sugg }}
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Budget --}}
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-2">Budget Minimum (Rp) <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-500 font-medium">Rp</span>
                            <input type="number" name="budget_min" value="{{ old('budget_min', 50000) }}" required min="50000"
                                class="input-dark w-full text-sm pl-9">
                        </div>
                        @error('budget_min')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-2">Budget Maksimum (Rp) <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-500 font-medium">Rp</span>
                            <input type="number" name="budget_max" value="{{ old('budget_max') }}" required min="50000"
                                class="input-dark w-full text-sm pl-9">
                        </div>
                        @error('budget_max')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Deadline & Revisions --}}
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-2">Deadline <span class="text-red-400">*</span></label>
                        <input type="date" name="deadline" value="{{ old('deadline') }}" required
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                            class="input-dark w-full text-sm">
                        @error('deadline')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-2">Maks. Revisi</label>
                        <select name="max_revisions" class="input-dark w-full text-sm">
                            @foreach([1, 2, 3, 5, 10] as $rev)
                            <option value="{{ $rev }}" {{ old('max_revisions', 3) == $rev ? 'selected' : '' }}>{{ $rev }}x Revisi</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- AI Matching Info --}}
        <div class="card p-5 mb-6 bg-primary-950/50 border-primary-800/30">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 bg-primary-500/20 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="fas fa-robot text-primary-400 text-sm"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-primary-300">AI Matching Otomatis</p>
                    <p class="text-xs text-primary-400/60 mt-1">Setelah proyek dibuat, AI kami akan langsung menganalisis skill yang Anda butuhkan dan mencarikan mahasiswa terbaik berdasarkan KRS, portfolio, dan riwayat proyek mereka.</p>
                </div>
            </div>
        </div>

        @if($errors->any())
        <div class="card p-4 mb-4 bg-red-900/20 border-red-500/30">
            @foreach($errors->all() as $error)
            <p class="text-sm text-red-400">• {{ $error }}</p>
            @endforeach
        </div>
        @endif

        <div class="flex gap-3">
            <a href="{{ route('client.dashboard') }}" class="btn-outline flex-1 py-3 rounded-xl text-sm font-semibold text-center">
                Batal
            </a>
            <button type="submit" class="btn-primary flex-1 py-3 rounded-xl text-sm font-semibold" id="submitBtn">
                <i class="fas fa-rocket mr-2"></i>Buat Proyek & Cari Kandidat
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const skills = [];

function addSkill(skill) {
    skill = skill.trim();
    if (!skill || skills.includes(skill)) return;
    skills.push(skill);
    renderSkills();
    document.getElementById('skillInput').value = '';
}

function removeSkill(skill) {
    const idx = skills.indexOf(skill);
    if (idx > -1) skills.splice(idx, 1);
    renderSkills();
}

function renderSkills() {
    const container = document.getElementById('skillTags');
    const inputs = document.getElementById('skillInputs');
    container.innerHTML = skills.map(s => `
        <span class="flex items-center gap-1.5 px-2.5 py-1 bg-primary-500/15 border border-primary-500/30 rounded-lg text-xs text-primary-300">
            ${s}
            <button type="button" onclick="removeSkill('${s}')" class="text-primary-500 hover:text-red-400 ml-1">×</button>
        </span>
    `).join('');
    inputs.innerHTML = skills.map(s => `<input type="hidden" name="required_skills[]" value="${s}">`).join('');
}

document.getElementById('skillInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        addSkill(this.value);
    }
});

document.getElementById('projectForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Membuat Proyek...';
    btn.disabled = true;
});
</script>
@endpush
