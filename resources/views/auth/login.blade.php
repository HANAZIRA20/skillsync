@extends('layouts.guest')
@section('title', 'Login')

@section('content')
<div class="glass rounded-2xl p-8 animate-fade-in shadow-2xl shadow-primary-950/50">
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="w-16 h-16 gradient-primary rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-primary-900/50" style="animation: float 3s ease-in-out infinite;">
            <i class="fas fa-bolt text-white text-2xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-white">Selamat Datang</h1>
        <p class="text-gray-400 text-sm mt-1">Login ke akun SkillSync Anda</p>
    </div>

    <!-- Error -->
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-900/40 border border-red-500/50 rounded-xl">
            <p class="text-red-300 text-sm">{{ $errors->first() }}</p>
        </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
        @csrf
        <div>
            <label class="text-sm text-gray-400 mb-2 block font-medium">Email</label>
            <div class="relative">
                <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com"
                    class="input-field w-full pl-11 pr-4 py-3 rounded-xl text-white placeholder-gray-600 text-sm" required>
            </div>
        </div>

        <div>
            <label class="text-sm text-gray-400 mb-2 block font-medium">Password</label>
            <div class="relative">
                <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                <input type="password" name="password" placeholder="••••••••"
                    class="input-field w-full pl-11 pr-4 py-3 rounded-xl text-white placeholder-gray-600 text-sm" required>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center space-x-2 cursor-pointer">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-700 text-primary-600 bg-gray-800 focus:ring-primary-600">
                <span class="text-sm text-gray-400">Ingat saya</span>
            </label>
        </div>

        <button type="submit" class="btn-primary w-full py-3.5 rounded-xl text-white font-semibold text-sm shadow-lg">
            <i class="fas fa-sign-in-alt mr-2"></i>Masuk ke Platform
        </button>
    </form>

    <div class="mt-6 text-center">
        <p class="text-gray-500 text-sm">Belum punya akun?
            <a href="{{ route('register') }}" class="text-primary-400 font-semibold hover:text-primary-300 transition-colors">Daftar Sekarang</a>
        </p>
    </div>

    <!-- Demo Accounts -->
    <div class="mt-6 p-4 bg-gray-900/60 rounded-xl border border-gray-800">
        <p class="text-xs text-gray-500 font-medium mb-3 flex items-center"><i class="fas fa-info-circle mr-2 text-primary-500"></i>Akun Demo</p>
        <div class="space-y-1.5 text-xs text-gray-500">
            <div class="flex justify-between"><span class="text-primary-400">Admin</span><span>admin@skillsync.id / password</span></div>
            <div class="flex justify-between"><span class="text-emerald-400">Mahasiswa</span><span>student@skillsync.id / password</span></div>
            <div class="flex justify-between"><span class="text-amber-400">Client</span><span>client@skillsync.id / password</span></div>
        </div>
    </div>
</div>
@endsection
