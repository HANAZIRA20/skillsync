<?php $__env->startSection('title', 'Proyek Saya'); ?>
<?php $__env->startSection('page-title', 'Proyek Saya'); ?>
<?php $__env->startSection('page-subtitle', 'Kelola semua proyek yang Anda buat'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 animate-fade-in">

    
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <?php $__currentLoopData = [
            ['label' => 'Total Dibuat', 'value' => $stats['total_posted'], 'icon' => 'fa-folder-plus', 'color' => 'primary'],
            ['label' => 'Selesai', 'value' => $stats['total_completed'], 'icon' => 'fa-check-double', 'color' => 'accent'],
            ['label' => 'Aktif', 'value' => $stats['active'], 'icon' => 'fa-spinner', 'color' => 'yellow'],
            ['label' => 'Total Bayar', 'value' => 'Rp ' . number_format($stats['total_spent'], 0, ',', '.'), 'icon' => 'fa-wallet', 'color' => 'green'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="card p-5">
            <div class="w-10 h-10 bg-<?php echo e($stat['color']); ?>-500/20 rounded-xl flex items-center justify-center mb-3">
                <i class="fas <?php echo e($stat['icon']); ?> text-<?php echo e($stat['color']); ?>-400"></i>
            </div>
            <div class="text-xl font-black text-white"><?php echo e($stat['value']); ?></div>
            <div class="text-xs text-gray-500 mt-1"><?php echo e($stat['label']); ?></div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="card overflow-hidden">
        <div class="p-6 border-b border-white/5 flex items-center justify-between">
            <h2 class="text-base font-bold text-white">Semua Proyek</h2>
            <a href="<?php echo e(route('client.create-project')); ?>" class="btn-primary px-4 py-2 rounded-xl text-sm font-semibold">
                <i class="fas fa-plus mr-2"></i>Proyek Baru
            </a>
        </div>

        <?php if($projects->isEmpty()): ?>
        <div class="text-center py-16">
            <div class="w-20 h-20 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-5">
                <i class="fas fa-folder-open text-3xl text-gray-600"></i>
            </div>
            <p class="text-lg font-semibold text-gray-400">Belum ada proyek</p>
            <a href="<?php echo e(route('client.create-project')); ?>" class="btn-primary inline-block mt-4 px-5 py-2.5 rounded-xl text-sm font-semibold">
                Buat Proyek Pertama
            </a>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Proyek</th>
                        <th class="text-left">Mahasiswa</th>
                        <th class="text-left">Budget</th>
                        <th class="text-left">Deadline</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <p class="text-sm font-semibold text-white"><?php echo e(Str::limit($project->title, 35)); ?></p>
                            <p class="text-xs text-gray-600 mt-0.5"><?php echo e($project->category); ?></p>
                        </td>
                        <td>
                            <?php if($project->selectedStudent): ?>
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-primary-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-primary-400"><?php echo e(strtoupper(substr($project->selectedStudent->user->name, 0, 1))); ?></span>
                                </div>
                                <span class="text-sm text-gray-300"><?php echo e($project->selectedStudent->user->name); ?></span>
                            </div>
                            <?php else: ?>
                            <span class="text-xs text-gray-600">Belum dipilih</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="text-sm font-semibold text-white">Rp <?php echo e(number_format($project->agreed_budget ?? $project->budget_max, 0, ',', '.')); ?></span>
                        </td>
                        <td>
                            <span class="text-sm text-gray-300"><?php echo e(\Carbon\Carbon::parse($project->deadline)->format('d M Y')); ?></span>
                        </td>
                        <td>
                            <?php
                                $badge = [
                                    'open' => ['class' => 'badge-open', 'label' => 'Open'],
                                    'waiting_payment' => ['class' => 'badge-waiting', 'label' => 'Tunggu Bayar'],
                                    'in_progress' => ['class' => 'badge-progress', 'label' => 'In Progress'],
                                    'in_review' => ['class' => 'badge-review', 'label' => 'Review'],
                                    'revision' => ['class' => 'badge-revision', 'label' => 'Revisi'],
                                    'completed' => ['class' => 'badge-completed', 'label' => 'Selesai'],
                                    'disputed' => ['class' => 'badge-disputed', 'label' => 'Sengketa'],
                                ][$project->status] ?? ['class' => 'badge-open', 'label' => $project->status];
                            ?>
                            <span class="badge <?php echo e($badge['class']); ?>"><?php echo e($badge['label']); ?></span>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <?php if($project->status === 'open'): ?>
                                <a href="<?php echo e(route('client.candidates', $project->id)); ?>" class="btn-primary px-3 py-1.5 rounded-lg text-xs">
                                    Kandidat
                                </a>
                                <?php elseif($project->status === 'waiting_payment'): ?>
                                <a href="<?php echo e(route('payment.escrow', $project->id)); ?>" class="btn-success px-3 py-1.5 rounded-lg text-xs font-semibold">
                                    Bayar
                                </a>
                                <?php elseif(in_array($project->status, ['in_progress', 'in_review', 'revision'])): ?>
                                <a href="<?php echo e(route('workroom.show', $project->id)); ?>" class="btn-primary px-3 py-1.5 rounded-lg text-xs">
                                    Workroom
                                </a>
                                <?php endif; ?>
                                <a href="<?php echo e(route('client.project-detail', $project->id)); ?>" class="btn-outline px-3 py-1.5 rounded-lg text-xs">
                                    Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <?php if($projects->hasPages()): ?>
        <div class="p-4 border-t border-white/5">
            <?php echo e($projects->links()); ?>

        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\SkillSync\resources\views/client/projects.blade.php ENDPATH**/ ?>