<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio {{ $student->user->name }} — SkillSync</title>
    <meta name="description" content="Portfolio mahasiswa {{ $student->user->name }} di SkillSync AI Career Platform.">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#eef2ff',100:'#e0e7ff',200:'#c7d2fe',300:'#a5b4fc',400:'#818cf8',500:'#6366f1',600:'#4f46e5',700:'#4338ca',800:'#3730a3',900:'#312e81',950:'#1e1b4b' },
                        accent: { 400:'#34d399',500:'#10b981' },
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255,255,255,0.05); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.1); }
        .gradient-primary { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #06b6d4 100%); }
        .gradient-text { background: linear-gradient(135deg, #818cf8, #34d399); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(99,102,241,0.2); }
    </style>
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen">
    <!-- Header -->
    <header class="bg-gray-900/80 backdrop-blur-md border-b border-gray-800 sticky top-0 z-30">
        <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="/" class="flex items-center space-x-3">
                <div class="w-9 h-9 gradient-primary rounded-xl flex items-center justify-center">
                    <i class="fas fa-bolt text-white"></i>
                </div>
                <span class="text-xl font-black gradient-text">SkillSync</span>
            </a>
            <a href="{{ route('login') }}" class="bg-primary-600 hover:bg-primary-500 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Masuk / Daftar
            </a>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-6 py-12">
        <!-- Profile Hero -->
        <div class="glass rounded-2xl p-8 mb-8 relative overflow-hidden">
            <div class="absolute inset-0 gradient-primary opacity-5"></div>
            <div class="relative flex items-start space-x-6">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($student->user->name) }}&background=6366f1&color=fff&size=128"
                     class="w-24 h-24 rounded-2xl ring-4 ring-primary-600/40" alt="Avatar">
                <div class="flex-1">
                    <h1 class="text-3xl font-black text-white mb-1">{{ $student->user->name }}</h1>
                    <p class="text-primary-400 font-medium mb-2">{{ $student->jurusan }} · {{ $student->universitas }}</p>
                    @if($student->bio)
                        <p class="text-gray-300 text-sm leading-relaxed mb-4">{{ $student->bio }}</p>
                    @endif
                    <div class="flex flex-wrap gap-3">
                        @if($student->linkedin_url)
                            <a href="{{ $student->linkedin_url }}" target="_blank" class="flex items-center space-x-2 text-blue-400 hover:text-blue-300 text-sm">
                                <i class="fab fa-linkedin"></i><span>LinkedIn</span>
                            </a>
                        @endif
                        @if($student->github_url)
                            <a href="{{ $student->github_url }}" target="_blank" class="flex items-center space-x-2 text-gray-400 hover:text-gray-300 text-sm">
                                <i class="fab fa-github"></i><span>GitHub</span>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-black gradient-text">{{ $portfolios->count() }}</div>
                    <div class="text-gray-500 text-sm">Proyek Selesai</div>
                    @if($student->average_rating)
                        <div class="mt-2 flex items-center justify-end space-x-1">
                            <i class="fas fa-star text-yellow-400 text-sm"></i>
                            <span class="font-bold text-white">{{ number_format($student->average_rating, 1) }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Portfolio Grid -->
        @if($portfolios->isEmpty())
            <div class="glass rounded-2xl p-12 text-center">
                <div class="w-16 h-16 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-briefcase text-2xl text-gray-600"></i>
                </div>
                <p class="text-gray-400">Belum ada portfolio publik.</p>
            </div>
        @else
            <h2 class="text-xl font-bold text-white mb-6">Portfolio Proyek</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($portfolios as $portfolio)
                    <div class="glass rounded-xl p-6 card-hover">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="font-bold text-white text-lg">{{ $portfolio->project->title ?? 'Proyek' }}</h3>
                                <p class="text-gray-500 text-sm">{{ $portfolio->project->category ?? '' }}</p>
                            </div>
                            @if($portfolio->is_verified)
                                <span class="bg-emerald-900/40 text-emerald-400 text-xs px-2 py-1 rounded-full border border-emerald-500/30 flex items-center space-x-1">
                                    <i class="fas fa-check-circle"></i><span>Terverifikasi</span>
                                </span>
                            @endif
                        </div>

                        @if($portfolio->description)
                            <p class="text-gray-400 text-sm mb-4">{{ Str::limit($portfolio->description, 120) }}</p>
                        @endif

                        <div class="flex items-center justify-between">
                            @if($portfolio->rating)
                                <div class="flex items-center space-x-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-sm {{ $i <= $portfolio->rating ? 'text-yellow-400' : 'text-gray-700' }}"></i>
                                    @endfor
                                    <span class="text-gray-500 text-xs ml-1">{{ number_format($portfolio->rating, 1) }}</span>
                                </div>
                            @endif
                            @if($portfolio->completed_at)
                                <span class="text-gray-600 text-xs">{{ \Carbon\Carbon::parse($portfolio->completed_at)->format('M Y') }}</span>
                            @endif
                        </div>

                        @if($portfolio->earned_amount)
                            <div class="mt-3 pt-3 border-t border-gray-800">
                                <span class="text-emerald-400 text-sm font-semibold">
                                    Rp {{ number_format($portfolio->earned_amount, 0, ',', '.') }}
                                </span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </main>

    <footer class="border-t border-gray-800 mt-16 py-6 text-center text-sm text-gray-600">
        © 2024 SkillSync AI Career Platform
    </footer>
</body>
</html>
