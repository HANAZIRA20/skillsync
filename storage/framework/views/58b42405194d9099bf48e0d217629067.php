<?php $__env->startSection('title', 'Proyek Saya'); ?>
<?php $__env->startSection('page-title', 'Proyek Saya'); ?>
<?php $__env->startSection('page-subtitle', 'Riwayat dan status semua proyek yang Anda kerjakan'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 animate-fade-in">

    
    <div class="card overflow-hidden">
        <div class="p-6 border-b border-white/5 flex items-center justify-between">
            <h2 class="text-base font-bold text-white">Semua Proyek</h2>
            <span class="badge badge-progress"><?php echo e($projects->total()); ?> Total</span>
        </div>

        <?php if($projects->isEmpty()): ?>
        <div class="text-center py-16">
            <div class="w-20 h-20 bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-5">
                <i class="fas fa-briefcase text-3xl text-gray-600"></i>
            </div>
            <p class="text-lg font-semibold text-gray-400">Belum ada proyek</p>
            <p class="text-sm text-gray-600 mt-1 mb-5">Proyek yang Anda kerjakan akan muncul di sini</p>
            <a href="<?php echo e(route('student.dashboard')); ?>" class="btn-primary inline-block px-5 py-2.5 rounded-xl text-sm font-semibold">
                Lihat Rekomendasi AI →
            </a>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Proyek</th>
                        <th class="text-left">Client</th>
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
                            <p class="text-sm font-semibold text-white"><?php echo e(Str::limit($project->title, 40)); ?></p>
                            <p class="text-xs text-gray-600 mt-0.5"><?php echo e($project->category); ?></p>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-accent-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-accent-400"><?php echo e(strtoupper(substr($project->client->user->name, 0, 1))); ?></span>
                                </div>
                                <span class="text-sm text-gray-300"><?php echo e($project->client->user->name); ?></span>
                            </div>
                        </td>
                        <td>
                            <span class="text-sm font-semibold text-white">Rp <?php echo e(number_format($project->agreed_budget ?? $project->budget_max, 0, ',', '.')); ?></span>
                        </td>
                        <td>
                            <?php
                                $deadline = \Carbon\Carbon::parse($project->deadline);
                                $isOverdue = $deadline->isPast() && !in_array($project->status, ['completed', 'cancelled']);
                            ?>
                            <span class="text-sm <?php echo e($isOverdue ? 'text-red-400' : 'text-gray-300'); ?>">
                                <?php echo e($deadline->format('d M Y')); ?>

                            </span>
                            <?php if($isOverdue): ?>
                            <p class="text-xs text-red-500">Melewati deadline</p>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                                $badges = [
                                    'open' => ['class' => 'badge-open', 'label' => 'Open'],
                                    'waiting_payment' => ['class' => 'badge-waiting', 'label' => 'Menunggu Bayar'],
                                    'in_progress' => ['class' => 'badge-progress', 'label' => 'In Progress'],
                                    'in_review' => ['class' => 'badge-review', 'label' => 'Review'],
                                    'revision' => ['class' => 'badge-revision', 'label' => 'Revisi'],
                                    'completed' => ['class' => 'badge-completed', 'label' => 'Selesai'],
                                    'cancelled' => ['class' => 'badge-revision', 'label' => 'Dibatalkan'],
                                    'disputed' => ['class' => 'badge-disputed', 'label' => 'Sengketa'],
                                ];
                                $badge = $badges[$project->status] ?? ['class' => 'badge-open', 'label' => $project->status];
                            ?>
                            <span class="badge <?php echo e($badge['class']); ?>"><?php echo e($badge['label']); ?></span>
                        </td>
                        <td>
                            <?php if(in_array($project->status, ['in_progress', 'in_review', 'revision'])): ?>
                            <a href="<?php echo e(route('workroom.show', $project->id)); ?>"
                                class="btn-primary px-3 py-1.5 rounded-lg text-xs font-semibold inline-block">
                                Buka Workroom
                            </a>
                            <?php elseif($project->status === 'completed'): ?>
                            <span class="text-xs text-accent-400 flex items-center gap-1">
                                <i class="fas fa-check-circle"></i> Selesai
                            </span>
                            <?php else: ?>
                            <span class="text-xs text-gray-600">—</span>
                            <?php endif; ?>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\SkillSync\resources\views/student/projects.blade.php ENDPATH**/ ?>