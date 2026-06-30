<?php $__env->startSection('title', 'Profil Saya'); ?>
<?php $__env->startSection('page-title', 'Profil Mahasiswa'); ?>
<?php $__env->startSection('page-subtitle', 'Kelola informasi dan keahlian Anda'); ?>

<?php $__env->startSection('content'); ?>
<div class="grid lg:grid-cols-3 gap-6 animate-fade-in">

    
    <div class="card p-6">
        <div class="text-center mb-6">
            <div class="w-20 h-20 bg-gradient-to-br from-primary-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-primary-500/30">
                <span class="text-3xl font-black text-white"><?php echo e(strtoupper(substr($user->name, 0, 1))); ?></span>
            </div>
            <h2 class="text-lg font-bold text-white"><?php echo e($user->name); ?></h2>
            <p class="text-sm text-gray-500"><?php echo e($user->email); ?></p>
            <?php if($student?->nim): ?>
            <p class="text-xs text-gray-600 mt-1">NIM: <?php echo e($student->nim); ?></p>
            <?php endif; ?>
        </div>

        <div class="space-y-3 mb-6">
            <div class="flex items-center gap-3 text-sm">
                <i class="fas fa-university w-5 text-center text-gray-600"></i>
                <span class="text-gray-400"><?php echo e($student?->university ?? 'Belum diisi'); ?></span>
            </div>
            <div class="flex items-center gap-3 text-sm">
                <i class="fas fa-book w-5 text-center text-gray-600"></i>
                <span class="text-gray-400"><?php echo e($student?->major ?? 'Belum diisi'); ?></span>
            </div>
            <div class="flex items-center gap-3 text-sm">
                <i class="fas fa-phone w-5 text-center text-gray-600"></i>
                <span class="text-gray-400"><?php echo e($user->phone ?? 'Belum diisi'); ?></span>
            </div>
            <?php if($student?->linkedin_url): ?>
            <div class="flex items-center gap-3 text-sm">
                <i class="fab fa-linkedin w-5 text-center text-blue-400"></i>
                <a href="<?php echo e($student->linkedin_url); ?>" target="_blank" class="text-blue-400 hover:underline truncate">LinkedIn</a>
            </div>
            <?php endif; ?>
            <?php if($student?->github_url): ?>
            <div class="flex items-center gap-3 text-sm">
                <i class="fab fa-github w-5 text-center text-gray-400"></i>
                <a href="<?php echo e($student->github_url); ?>" target="_blank" class="text-gray-400 hover:text-white truncate">GitHub</a>
            </div>
            <?php endif; ?>
        </div>

        
        <div class="grid grid-cols-3 gap-3 pt-4 border-t border-white/5">
            <div class="text-center">
                <div class="text-xl font-black text-white"><?php echo e($student?->total_projects ?? 0); ?></div>
                <div class="text-[10px] text-gray-600">Proyek</div>
            </div>
            <div class="text-center">
                <div class="text-xl font-black text-white"><?php echo e(number_format($student?->average_rating ?? 0, 1)); ?></div>
                <div class="text-[10px] text-gray-600">Rating</div>
            </div>
            <div class="text-center">
                <div class="text-xl font-black text-white"><?php echo e($student?->skills->count() ?? 0); ?></div>
                <div class="text-[10px] text-gray-600">Skills</div>
            </div>
        </div>
    </div>

    
    <div class="lg:col-span-2 space-y-6">

        
        <div class="card p-6">
            <h2 class="text-base font-bold text-white mb-5 flex items-center gap-2">
                <i class="fas fa-edit text-primary-400"></i> Edit Profil
            </h2>
            <form action="<?php echo e(route('student.profile.update')); ?>" method="POST">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-gray-500 mb-2 font-medium">Nomor Telepon</label>
                        <input type="text" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>"
                            placeholder="+62 8xx xxxx xxxx"
                            class="input-dark w-full">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-2 font-medium">URL LinkedIn</label>
                        <input type="url" name="linkedin_url" value="<?php echo e(old('linkedin_url', $student?->linkedin_url)); ?>"
                            placeholder="https://linkedin.com/in/..."
                            class="input-dark w-full">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-2 font-medium">URL GitHub</label>
                        <input type="url" name="github_url" value="<?php echo e(old('github_url', $student?->github_url)); ?>"
                            placeholder="https://github.com/..."
                            class="input-dark w-full">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-xs text-gray-500 mb-2 font-medium">Bio / Deskripsi Diri</label>
                    <textarea name="bio" rows="4"
                        placeholder="Ceritakan tentang diri Anda, keahlian, dan pengalaman..."
                        class="input-dark w-full resize-none"><?php echo e(old('bio', $student?->bio)); ?></textarea>
                    <p class="text-xs text-gray-600 mt-1">Maks. 500 karakter</p>
                </div>
                <?php if($errors->any()): ?>
                <div class="mt-4 p-3 bg-red-500/10 border border-red-500/30 rounded-xl">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <p class="text-sm text-red-400">• <?php echo e($error); ?></p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
                <div class="mt-5 flex justify-end">
                    <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        
        <div class="card p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fas fa-brain text-purple-400"></i> Skills Terdeteksi AI
                </h2>
                <a href="<?php echo e(route('student.krs')); ?>" class="text-xs text-primary-400 hover:text-primary-300 transition-colors">
                    Update KRS →
                </a>
            </div>

            <?php if($student?->skills->isEmpty()): ?>
            <div class="text-center py-8">
                <div class="w-12 h-12 bg-white/5 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-brain text-gray-600 text-xl"></i>
                </div>
                <p class="text-gray-500 text-sm">Belum ada skill terdeteksi</p>
                <a href="<?php echo e(route('student.krs')); ?>" class="btn-primary inline-block mt-3 px-4 py-2 rounded-xl text-sm font-semibold">
                    Upload KRS Sekarang
                </a>
            </div>
            <?php else: ?>
            <div class="flex flex-wrap gap-2">
                <?php $__currentLoopData = $student->skills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center gap-2 px-3 py-1.5 bg-primary-500/10 border border-primary-500/20 rounded-xl">
                    <span class="text-sm text-primary-300 font-medium"><?php echo e($skill->skill_name); ?></span>
                    <?php if($skill->proficiency_level): ?>
                    <span class="text-[10px] px-1.5 py-0.5 rounded-full
                        <?php if($skill->proficiency_level >= 4): ?> bg-accent-500/20 text-accent-400
                        <?php elseif($skill->proficiency_level >= 2): ?> bg-yellow-500/20 text-yellow-400
                        <?php else: ?> bg-gray-500/20 text-gray-400 <?php endif; ?>">
                        <?php if($skill->proficiency_level >= 4): ?> Expert
                        <?php elseif($skill->proficiency_level >= 2): ?> Menengah
                        <?php else: ?> Pemula <?php endif; ?>
                    </span>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
        </div>

        
        <?php if($student?->portfolios->isNotEmpty()): ?>
        <div class="card p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fas fa-star text-yellow-400"></i> Portfolio
                </h2>
                <a href="<?php echo e(route('student.portfolio')); ?>" class="text-xs text-primary-400 hover:text-primary-300 transition-colors">Kelola Portfolio →</a>
            </div>
            <div class="grid sm:grid-cols-2 gap-3">
                <?php $__currentLoopData = $student->portfolios->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $portfolio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="p-3 bg-white/3 rounded-xl border border-white/5">
                    <h3 class="text-sm font-semibold text-white mb-1"><?php echo e($portfolio->project?->title ?? 'Proyek'); ?></h3>
                    <p class="text-xs text-gray-500 line-clamp-2"><?php echo e($portfolio->description); ?></p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-[10px] text-primary-400"><?php echo e($portfolio->project?->category); ?></span>
                        <?php if($portfolio->is_visible): ?>
                        <span class="badge badge-open ml-auto">Publik</span>
                        <?php else: ?>
                        <span class="badge badge-review ml-auto">Private</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\SkillSync\resources\views/student/profile.blade.php ENDPATH**/ ?>