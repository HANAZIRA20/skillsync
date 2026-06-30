<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'SkillSync'); ?> — AI Career Platform</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .gradient-primary { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #06b6d4 100%); }
        .gradient-text { background: linear-gradient(135deg, #818cf8, #34d399); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .glass { background: rgba(255,255,255,0.05); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.08); }
        .particle { position: absolute; border-radius: 50%; animation: float-particle linear infinite; opacity: 0.4; }
        @keyframes float-particle { 0%{transform:translateY(100vh) rotate(0deg);opacity:0} 10%{opacity:0.4} 90%{opacity:0.4} 100%{transform:translateY(-100px) rotate(720deg);opacity:0} }
        .input-field { background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);transition:all 0.3s; }
        .input-field:focus { border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,0.15);outline:none;background:rgba(99,102,241,0.05); }
        .btn-primary { background: linear-gradient(135deg, #6366f1, #8b5cf6); transition: all 0.3s ease; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(99,102,241,0.4); }
    </style>
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen flex items-center justify-center relative overflow-hidden">
    <!-- Animated Background -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-900 rounded-full blur-3xl opacity-30"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-purple-900 rounded-full blur-3xl opacity-30"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-primary-950 rounded-full blur-3xl opacity-20"></div>
        <!-- Floating particles -->
        <div class="particle w-2 h-2 bg-primary-500" style="left:10%;animation-duration:15s;animation-delay:0s;"></div>
        <div class="particle w-1 h-1 bg-accent-400" style="left:25%;animation-duration:20s;animation-delay:5s;"></div>
        <div class="particle w-3 h-3 bg-primary-400" style="left:50%;animation-duration:18s;animation-delay:2s;"></div>
        <div class="particle w-1.5 h-1.5 bg-purple-400" style="left:75%;animation-duration:22s;animation-delay:8s;"></div>
        <div class="particle w-2 h-2 bg-accent-500" style="left:90%;animation-duration:16s;animation-delay:3s;"></div>
    </div>

    <!-- Logo Top Left -->
    <div class="absolute top-8 left-8 flex items-center space-x-3">
        <div class="w-10 h-10 gradient-primary rounded-xl flex items-center justify-center shadow-lg">
            <i class="fas fa-bolt text-white"></i>
        </div>
        <div>
            <span class="text-xl font-black gradient-text">SkillSync</span>
            <p class="text-xs text-gray-600">AI Career Platform</p>
        </div>
    </div>

    <!-- Content -->
    <div class="relative z-10 w-full max-w-md px-4">
        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <!-- Bottom Tagline -->
    <div class="absolute bottom-8 text-center w-full">
        <p class="text-xs text-gray-700">© 2024 SkillSync · AI-Powered Micro-Tasking Platform</p>
    </div>
</body>
</html>
<?php /**PATH D:\laragon\www\SkillSync\resources\views/layouts/guest.blade.php ENDPATH**/ ?>