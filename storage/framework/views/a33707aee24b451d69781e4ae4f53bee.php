<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'SkillSync'); ?> — AI-Powered Career Platform</title>
    <meta name="description" content="<?php echo $__env->yieldContent('description', 'Platform marketplace AI yang menghubungkan mahasiswa dengan UMKM untuk proyek nyata.'); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#eef2ff',100:'#e0e7ff',200:'#c7d2fe',300:'#a5b4fc',400:'#818cf8',500:'#6366f1',600:'#4f46e5',700:'#4338ca',800:'#3730a3',900:'#312e81',950:'#1e1b4b' },
                        accent: { 400:'#34d399',500:'#10b981',600:'#059669' },
                        gold: { 400:'#fbbf24',500:'#f59e0b',600:'#d97706' },
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'slide-up': 'slideUp 0.4s ease-out',
                        'pulse-glow': 'pulseGlow 2s infinite',
                        'float': 'float 3s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: '0', transform: 'translateY(10px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                        slideUp: { '0%': { opacity: '0', transform: 'translateY(20px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } },
                        pulseGlow: { '0%,100%': { boxShadow: '0 0 15px rgba(99,102,241,0.3)' }, '50%': { boxShadow: '0 0 30px rgba(99,102,241,0.6)' } },
                        float: { '0%,100%': { transform: 'translateY(0px)' }, '50%': { transform: 'translateY(-8px)' } },
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #1e1b4b; }
        ::-webkit-scrollbar-thumb { background: #6366f1; border-radius: 3px; }
        .glass { background: rgba(255,255,255,0.05); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.1); }
        .glass-dark { background: rgba(15,10,40,0.8); backdrop-filter: blur(20px); border: 1px solid rgba(99,102,241,0.2); }
        .gradient-primary { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #06b6d4 100%); }
        .gradient-text { background: linear-gradient(135deg, #818cf8, #34d399); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .gradient-border { border: 1px solid; border-image: linear-gradient(135deg, #6366f1, #34d399) 1; }
        .sidebar-link { transition: all 0.2s ease; }
        .sidebar-link:hover { background: rgba(99,102,241,0.2); transform: translateX(4px); }
        .sidebar-link.active { background: rgba(99,102,241,0.3); border-left: 3px solid #6366f1; }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(99,102,241,0.2); }
        .btn-primary { background: linear-gradient(135deg, #6366f1, #8b5cf6); transition: all 0.3s ease; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(99,102,241,0.4); }
        .stat-card { position: relative; overflow: hidden; }
        .stat-card::before { content:''; position:absolute; top:-50%; right:-50%; width:100%; height:100%; border-radius:50%; background:rgba(99,102,241,0.1); }
        .badge-ai { background: linear-gradient(135deg, rgba(99,102,241,0.3), rgba(139,92,246,0.3)); border: 1px solid rgba(99,102,241,0.4); }
        .score-ring { background: conic-gradient(#6366f1 0%, #34d399 var(--score), transparent var(--score)); }
        @keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
        .shimmer { background:linear-gradient(90deg,transparent,rgba(255,255,255,0.05),transparent); background-size:200% 100%; animation: shimmer 2s infinite; }
        .notification { animation: slideDown 0.3s ease-out; }
        @keyframes slideDown { from{transform:translateY(-20px);opacity:0} to{transform:translateY(0);opacity:1} }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-900 border-r border-gray-800 flex flex-col fixed h-full z-40 overflow-y-auto">
        <!-- Logo -->
        <div class="p-6 border-b border-gray-800">
            <a href="/" class="flex items-center space-x-3 group">
                <div class="w-10 h-10 gradient-primary rounded-xl flex items-center justify-center shadow-lg shadow-primary-900/50 group-hover:scale-110 transition-transform">
                    <i class="fas fa-bolt text-white text-lg"></i>
                </div>
                <div>
                    <span class="text-xl font-black gradient-text">SkillSync</span>
                    <p class="text-xs text-gray-500">AI Career Platform</p>
                </div>
            </a>
        </div>

        <!-- User Info -->
        <div class="p-4 border-b border-gray-800">
            <div class="flex items-center space-x-3">
                <img src="<?php echo e(Auth::user()->avatar_url); ?>" class="w-10 h-10 rounded-full ring-2 ring-primary-600" alt="Avatar">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-white truncate"><?php echo e(Auth::user()->name); ?></p>
                    <span class="text-xs px-2 py-0.5 rounded-full
                        <?php echo e(Auth::user()->role === 'mahasiswa' ? 'bg-primary-900 text-primary-300' : ''); ?>

                        <?php echo e(Auth::user()->role === 'client' ? 'bg-emerald-900 text-emerald-300' : ''); ?>

                        <?php echo e(Auth::user()->role === 'admin' ? 'bg-red-900 text-red-300' : ''); ?>

                    ">
                        <?php echo e(ucfirst(Auth::user()->role)); ?>

                    </span>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-1">
            <?php if(Auth::user()->isMahasiswa()): ?>
                <p class="text-xs text-gray-500 uppercase tracking-widest mb-3 px-3">Menu Mahasiswa</p>
                <a href="<?php echo e(route('student.dashboard')); ?>" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg text-gray-300 hover:text-white <?php echo e(request()->routeIs('student.dashboard') ? 'active text-white' : ''); ?>">
                    <i class="fas fa-home w-4"></i><span>Dashboard</span>
                </a>
                <a href="<?php echo e(route('student.krs')); ?>" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg text-gray-300 hover:text-white <?php echo e(request()->routeIs('student.krs*') ? 'active text-white' : ''); ?>">
                    <i class="fas fa-brain w-4"></i><span>Upload KRS</span>
                    <?php if(Auth::user()->student?->krs_status === 'not_uploaded'): ?>
                        <span class="ml-auto bg-amber-500 text-xs px-1.5 py-0.5 rounded-full text-black font-bold">!</span>
                    <?php endif; ?>
                </a>
                <a href="<?php echo e(route('student.portfolio')); ?>" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg text-gray-300 hover:text-white <?php echo e(request()->routeIs('student.portfolio') ? 'active text-white' : ''); ?>">
                    <i class="fas fa-briefcase w-4"></i><span>Portfolio</span>
                </a>
                <a href="<?php echo e(route('student.projects')); ?>" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg text-gray-300 hover:text-white <?php echo e(request()->routeIs('student.projects') ? 'active text-white' : ''); ?>">
                    <i class="fas fa-tasks w-4"></i><span>Proyek Saya</span>
                </a>
            <?php elseif(Auth::user()->isClient()): ?>
                <p class="text-xs text-gray-500 uppercase tracking-widest mb-3 px-3">Menu Client</p>
                <a href="<?php echo e(route('client.dashboard')); ?>" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg text-gray-300 hover:text-white <?php echo e(request()->routeIs('client.dashboard') ? 'active text-white' : ''); ?>">
                    <i class="fas fa-home w-4"></i><span>Dashboard</span>
                </a>
                <a href="<?php echo e(route('client.create-project')); ?>" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg text-gray-300 hover:text-white <?php echo e(request()->routeIs('client.create-project') ? 'active text-white' : ''); ?>">
                    <i class="fas fa-plus-circle w-4"></i><span>Buat Proyek</span>
                </a>
                <a href="<?php echo e(route('client.projects')); ?>" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg text-gray-300 hover:text-white <?php echo e(request()->routeIs('client.projects') ? 'active text-white' : ''); ?>">
                    <i class="fas fa-folder-open w-4"></i><span>Proyek Saya</span>
                </a>
            <?php elseif(Auth::user()->isAdmin()): ?>
                <p class="text-xs text-gray-500 uppercase tracking-widest mb-3 px-3">Menu Admin</p>
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg text-gray-300 hover:text-white <?php echo e(request()->routeIs('admin.dashboard') ? 'active text-white' : ''); ?>">
                    <i class="fas fa-shield-alt w-4"></i><span>Dashboard Admin</span>
                </a>
                <a href="<?php echo e(route('analytics.dashboard')); ?>" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg text-gray-300 hover:text-white <?php echo e(request()->routeIs('analytics.*') ? 'active text-white' : ''); ?>">
                    <i class="fas fa-chart-line w-4"></i><span>Analytics OLAP</span>
                </a>
            <?php endif; ?>

            <!-- Common Links -->
            <div class="pt-4 border-t border-gray-800 mt-4">
                <?php if(!Auth::user()->isAdmin()): ?>
                    <a href="<?php echo e(route('analytics.dashboard')); ?>" class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-lg text-gray-300 hover:text-white <?php echo e(request()->routeIs('analytics.*') ? 'active text-white' : ''); ?>">
                        <i class="fas fa-chart-bar w-4"></i><span>Analytics</span>
                    </a>
                <?php endif; ?>
            </div>
        </nav>

        <!-- Logout -->
        <div class="p-4 border-t border-gray-800">
            <form action="<?php echo e(route('logout')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-gray-400 hover:text-red-400 hover:bg-red-900/20 transition-all">
                    <i class="fas fa-sign-out-alt w-4"></i><span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="ml-64 flex-1 flex flex-col min-h-screen">
        <!-- Top Bar -->
        <header class="bg-gray-900/80 backdrop-blur-md border-b border-gray-800 px-8 py-4 flex items-center justify-between sticky top-0 z-30">
            <div>
                <h1 class="text-xl font-bold text-white"><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></h1>
                <p class="text-sm text-gray-500"><?php echo $__env->yieldContent('page-subtitle', ''); ?></p>
            </div>
            <div class="flex items-center space-x-4">
                <!-- AI Badge -->
                <div class="badge-ai px-3 py-1.5 rounded-full flex items-center space-x-2">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    <span class="text-xs text-primary-300 font-medium">AI Engine Active</span>
                </div>
                <img src="<?php echo e(Auth::user()->avatar_url); ?>" class="w-9 h-9 rounded-full ring-2 ring-primary-700" alt="Avatar">
            </div>
        </header>

        <!-- Flash Messages -->
        <?php if(session('success')): ?>
            <div class="notification mx-8 mt-4 p-4 bg-emerald-900/40 border border-emerald-500/50 rounded-xl text-emerald-300 flex items-center space-x-3">
                <i class="fas fa-check-circle text-emerald-400"></i>
                <span><?php echo e(session('success')); ?></span>
            </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="notification mx-8 mt-4 p-4 bg-red-900/40 border border-red-500/50 rounded-xl text-red-300 flex items-center space-x-3">
                <i class="fas fa-exclamation-circle text-red-400"></i>
                <span><?php echo e(session('error')); ?></span>
            </div>
        <?php endif; ?>
        <?php if(session('info')): ?>
            <div class="notification mx-8 mt-4 p-4 bg-blue-900/40 border border-blue-500/50 rounded-xl text-blue-300 flex items-center space-x-3">
                <i class="fas fa-info-circle text-blue-400"></i>
                <span><?php echo e(session('info')); ?></span>
            </div>
        <?php endif; ?>

        <!-- Page Content -->
        <main class="flex-1 p-8">
            <?php echo $__env->yieldContent('content'); ?>
        </main>

        <!-- Footer -->
        <footer class="border-t border-gray-800 px-8 py-4 text-center text-xs text-gray-600">
            © 2024 SkillSync AI Career Platform · Powered by AI Matching Engine
        </footer>
    </div>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\laragon\www\SkillSync\resources\views/layouts/app.blade.php ENDPATH**/ ?>