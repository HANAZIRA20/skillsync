@extends('layouts.guest')
@section('title', 'Daftar')

@section('content')
<div class="glass rounded-2xl p-8 animate-fade-in shadow-2xl shadow-primary-950/50">
    <div class="text-center mb-6">
        <div class="w-14 h-14 gradient-primary rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg">
            <i class="fas fa-user-plus text-white text-xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-white">Buat Akun</h1>
        <p class="text-gray-400 text-sm">Bergabung dengan SkillSync AI Platform</p>
    </div>

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-900/40 border border-red-500/50 rounded-xl">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-red-300 text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register.post') }}" method="POST" id="registerForm" class="space-y-4">
        @csrf

        <!-- Role Selection -->
        <div>
            <label class="text-sm text-gray-400 mb-2 block font-medium">Daftar Sebagai</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="role-btn cursor-pointer">
                    <input type="radio" name="role" value="mahasiswa" class="hidden" {{ old('role','mahasiswa')==='mahasiswa'?'checked':'' }}>
                    <div class="role-card p-4 rounded-xl border border-gray-700 text-center transition-all hover:border-primary-500 {{ old('role','mahasiswa')==='mahasiswa'?'border-primary-500 bg-primary-900/30':'' }}">
                        <i class="fas fa-graduation-cap text-2xl text-primary-400 mb-2 block"></i>
                        <span class="text-sm font-medium text-white">Mahasiswa</span>
                        <p class="text-xs text-gray-500 mt-1">Cari proyek & bangun karir</p>
                    </div>
                </label>
                <label class="role-btn cursor-pointer">
                    <input type="radio" name="role" value="client" class="hidden" {{ old('role')==='client'?'checked':'' }}>
                    <div class="role-card p-4 rounded-xl border border-gray-700 text-center transition-all hover:border-emerald-500 {{ old('role')==='client'?'border-emerald-500 bg-emerald-900/30':'' }}">
                        <i class="fas fa-building text-2xl text-emerald-400 mb-2 block"></i>
                        <span class="text-sm font-medium text-white">Client / UMKM</span>
                        <p class="text-xs text-gray-500 mt-1">Posting proyek & rekrut talent</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Name & Email -->
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="text-xs text-gray-400 mb-1 block">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama Anda"
                    class="input-field w-full px-4 py-2.5 rounded-xl text-white placeholder-gray-600 text-sm" required>
            </div>
            <div>
                <label class="text-xs text-gray-400 mb-1 block">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="email@domain.com"
                    class="input-field w-full px-4 py-2.5 rounded-xl text-white placeholder-gray-600 text-sm" required>
            </div>
        </div>

        <!-- Password -->
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="text-xs text-gray-400 mb-1 block">Password</label>
                <input type="password" name="password" placeholder="Min. 8 karakter"
                    class="input-field w-full px-4 py-2.5 rounded-xl text-white placeholder-gray-600 text-sm" required>
            </div>
            <div>
                <label class="text-xs text-gray-400 mb-1 block">Konfirmasi</label>
                <input type="password" name="password_confirmation" placeholder="Ulangi password"
                    class="input-field w-full px-4 py-2.5 rounded-xl text-white placeholder-gray-600 text-sm" required>
            </div>
        </div>

        <!-- Student Fields -->
        <div id="studentFields" class="{{ old('role','mahasiswa')==='mahasiswa'?'':'hidden' }} space-y-3">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">NIM</label>
                    <input type="text" name="nim" value="{{ old('nim') }}" placeholder="NIM Anda"
                        class="input-field w-full px-4 py-2.5 rounded-xl text-white placeholder-gray-600 text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Universitas <span class="text-red-400">*</span></label>
                    <input type="text" name="universitas" value="{{ old('universitas') }}" placeholder="Nama Universitas"
                        class="input-field w-full px-4 py-2.5 rounded-xl text-white placeholder-gray-600 text-sm">
                </div>
            </div>
            <div>
                <label class="text-xs text-gray-400 mb-1 block">Jurusan <span class="text-red-400">*</span></label>
                <input type="text" name="jurusan" value="{{ old('jurusan') }}" placeholder="Jurusan / Program Studi"
                    class="input-field w-full px-4 py-2.5 rounded-xl text-white placeholder-gray-600 text-sm">
            </div>
        </div>

        <!-- Client Fields -->
        <div id="clientFields" class="{{ old('role')==='client'?'':'hidden' }} space-y-3">
            <div>
                <label class="text-xs text-gray-400 mb-1 block">Nama Perusahaan / UMKM <span class="text-red-400">*</span></label>
                <input type="text" name="company_name" value="{{ old('company_name') }}" placeholder="PT / CV / Toko / Brand Anda"
                    class="input-field w-full px-4 py-2.5 rounded-xl text-white placeholder-gray-600 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Industri <span class="text-red-400">*</span></label>
                    <select name="industry" class="input-field w-full px-4 py-2.5 rounded-xl text-white text-sm">
                        <option value="">Pilih Industri</option>
                        <option value="Teknologi" {{ old('industry')==='Teknologi'?'selected':'' }}>Teknologi</option>
                        <option value="E-Commerce" {{ old('industry')==='E-Commerce'?'selected':'' }}>E-Commerce</option>
                        <option value="Kuliner" {{ old('industry')==='Kuliner'?'selected':'' }}>Kuliner & F&B</option>
                        <option value="Fashion" {{ old('industry')==='Fashion'?'selected':'' }}>Fashion & Retail</option>
                        <option value="Kesehatan" {{ old('industry')==='Kesehatan'?'selected':'' }}>Kesehatan</option>
                        <option value="Pendidikan" {{ old('industry')==='Pendidikan'?'selected':'' }}>Pendidikan</option>
                        <option value="Properti" {{ old('industry')==='Properti'?'selected':'' }}>Properti</option>
                        <option value="Jasa" {{ old('industry')==='Jasa'?'selected':'' }}>Jasa & Konsultan</option>
                        <option value="Media" {{ old('industry')==='Media'?'selected':'' }}>Media & Kreatif</option>
                        <option value="Lainnya" {{ old('industry')==='Lainnya'?'selected':'' }}>Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Kota</label>
                    <input type="text" name="city" value="{{ old('city') }}" placeholder="Kota Anda"
                        class="input-field w-full px-4 py-2.5 rounded-xl text-white placeholder-gray-600 text-sm">
                </div>
            </div>
        </div>

        <button type="submit" class="btn-primary w-full py-3.5 rounded-xl text-white font-semibold text-sm shadow-lg mt-2">
            <i class="fas fa-rocket mr-2"></i>Buat Akun Sekarang
        </button>
    </form>

    <p class="text-center text-gray-500 text-sm mt-4">
        Sudah punya akun? <a href="{{ route('login') }}" class="text-primary-400 font-semibold hover:text-primary-300">Masuk</a>
    </p>
</div>

@push('scripts')
<script>
    const radios = document.querySelectorAll('input[name="role"]');
    const studentFields = document.getElementById('studentFields');
    const clientFields = document.getElementById('clientFields');
    const roleCards = document.querySelectorAll('.role-card');

    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            roleCards.forEach(c => { c.className = c.className.replace(/border-primary-500 bg-primary-900\/30|border-emerald-500 bg-emerald-900\/30/g, ''); });
            if (this.value === 'mahasiswa') {
                studentFields.classList.remove('hidden');
                clientFields.classList.add('hidden');
                this.closest('.role-btn').querySelector('.role-card').classList.add('border-primary-500', 'bg-primary-900/30');
            } else {
                clientFields.classList.remove('hidden');
                studentFields.classList.add('hidden');
                this.closest('.role-btn').querySelector('.role-card').classList.add('border-emerald-500', 'bg-emerald-900/30');
            }
        });
    });
</script>
@endpush
@endsection
