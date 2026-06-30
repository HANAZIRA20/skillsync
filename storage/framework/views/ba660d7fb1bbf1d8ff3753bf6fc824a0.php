<?php $__env->startSection('title', 'Portfolio'); ?>
<?php $__env->startSection('page-title', 'Portfolio'); ?>
<?php $__env->startSection('page-subtitle', 'Showcase karya terbaik Anda untuk para client'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 animate-fade-in">

    
    <div class="card p-5 flex items-center gap-4">
        <div class="w-10 h-10 bg-accent-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-link text-accent-400"></i>
        </div>
        <div class="flex-1">
            <p class="text-sm font-semibold text-white">Link Portfolio Publik</p>
            <p class="text-xs text-gray-500 mt-0.5">Bagikan link ini kepada client atau rekruter</p>
        </div>
        <div class="flex items-center gap-2">
            <code class="text-xs bg-white/5 px-3 py-1.5 rounded-lg text-gray-400 border border-white/5">
                <?php echo e(url('/portfolio/' . auth()->id())); ?>

            </code>
            <button onclick="copyLink()" class="btn-outline px-3 py-1.5 rounded-xl text-xs font-medium">
                <i class="fas fa-copy mr-1"></i>Copy
            </button>
        </div>
    </div>

    
    <?php if(empty($portfolios) || count($portfolios) === 0): ?>
    <div class="card p-16 text-center">
        <div class="w-20 h-20 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-5">
            <i class="fas fa-star text-3xl text-gray-600"></i>
        </div>
        <p class="text-lg font-semibold text-gray-400">Portfolio kosong</p>
        <p class="text-sm text-gray-600 mt-1 mb-5">Selesaikan proyek untuk menambah portfolio Anda</p>
        <a href="<?php echo e(route('student.projects')); ?>" class="btn-primary inline-block px-5 py-2.5 rounded-xl text-sm font-semibold">
            Lihat Proyek Saya
        </a>
    </div>
    <?php else: ?>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php $__currentLoopData = $portfolios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $portfolio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="card overflow-hidden group">
            
            <div class="h-36 bg-gradient-to-br
                <?php
                    $gradients = ['from-primary-900/60 to-purple-900/60', 'from-accent-900/60 to-teal-900/60', 'from-rose-900/60 to-pink-900/60', 'from-amber-900/60 to-yellow-900/60'];
                    echo $gradients[$loop->index % count($gradients)];
                ?>
                flex items-center justify-center relative overflow-hidden">
                <i class="fas fa-code text-4xl text-white/20"></i>
                <div class="absolute top-3 right-3">
                    <?php if($portfolio->is_public): ?>
                    <span class="badge badge-open">Publik</span>
                    <?php else: ?>
                    <span class="badge badge-review">Private</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="p-5">
                <h3 class="text-sm font-bold text-white mb-1 group-hover:text-primary-300 transition-colors">
                    <?php echo e($portfolio->project?->title ?? 'Proyek'); ?>

                </h3>
                <?php if($portfolio->project?->category): ?>
                <span class="text-[10px] px-2 py-0.5 bg-primary-500/10 text-primary-400 rounded-full border border-primary-500/20">
                    <?php echo e($portfolio->project->category); ?>

                </span>
                <?php endif; ?>

                <?php if($portfolio->description): ?>
                <p class="text-xs text-gray-500 mt-3 line-clamp-2"><?php echo e($portfolio->description); ?></p>
                <?php endif; ?>

                <?php if($portfolio->tech_stack && count($portfolio->tech_stack) > 0): ?>
                <div class="flex flex-wrap gap-1 mt-3">
                    <?php $__currentLoopData = array_slice($portfolio->tech_stack, 0, 4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tech): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="text-[10px] px-2 py-0.5 bg-white/5 text-gray-400 rounded-full border border-white/5"><?php echo e($tech); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>

                <div class="flex items-center justify-between mt-4 pt-4 border-t border-white/5">
                    <?php if($portfolio->rating_received): ?>
                    <div class="flex items-center gap-1">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star text-xs <?php echo e($i <= $portfolio->rating_received ? 'text-yellow-400' : 'text-gray-700'); ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <?php else: ?>
                    <span class="text-xs text-gray-600">Belum ada rating</span>
                    <?php endif; ?>

                    <form action="<?php echo e(route('student.portfolio.toggle', $portfolio->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="text-xs <?php echo e($portfolio->is_public ? 'text-gray-500 hover:text-red-400' : 'text-primary-400 hover:text-primary-300'); ?> transition-colors">
                            <i class="fas fa-<?php echo e($portfolio->is_public ? 'eye-slash' : 'eye'); ?> mr-1"></i>
                            <?php echo e($portfolio->is_public ? 'Sembunyikan' : 'Tampilkan'); ?>

                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function copyLink() {
    navigator.clipboard.writeText('<?php echo e(url('/portfolio/' . auth()->id())); ?>').then(() => {
        alert('Link berhasil disalin!');
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\SkillSync\resources\views/student/portfolio.blade.php ENDPATH**/ ?>