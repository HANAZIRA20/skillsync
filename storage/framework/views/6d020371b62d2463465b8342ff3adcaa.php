<?php $__env->startSection('title', 'Admin Dashboard'); ?>
<?php $__env->startSection('page-title', 'Admin Dashboard'); ?>
<?php $__env->startSection('page-subtitle', 'Pantau dan kelola platform SkillSync'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 animate-fade-in">

    
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <?php $__currentLoopData = [
            ['label' => 'Total User', 'value' => $stats['total_users'], 'icon' => 'fa-users', 'color' => 'primary', 'sub' => $stats['total_students'] . ' mahasiswa, ' . $stats['total_clients'] . ' client'],
            ['label' => 'Total Proyek', 'value' => $stats['total_projects'], 'icon' => 'fa-briefcase', 'color' => 'purple', 'sub' => $stats['active_projects'] . ' aktif'],
            ['label' => 'Revenue Platform', 'value' => 'Rp ' . number_format($stats['total_revenue'], 0, ',', '.'), 'icon' => 'fa-coins', 'color' => 'yellow', 'sub' => $stats['completed_projects'] . ' proyek selesai'],
            ['label' => 'Dana Ditahan', 'value' => 'Rp ' . number_format($stats['held_amount'], 0, ',', '.'), 'icon' => 'fa-shield-alt', 'color' => 'accent', 'sub' => 'Dalam escrow'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="stat-card card p-5">
            <div class="w-10 h-10 bg-<?php echo e($stat['color']); ?>-500/20 rounded-xl flex items-center justify-center mb-3">
                <i class="fas <?php echo e($stat['icon']); ?> text-<?php echo e($stat['color']); ?>-400"></i>
            </div>
            <div class="text-xl font-black text-white"><?php echo e($stat['value']); ?></div>
            <div class="text-xs font-semibold text-gray-400 mt-0.5"><?php echo e($stat['label']); ?></div>
            <div class="text-[10px] text-gray-600 mt-0.5"><?php echo e($stat['sub']); ?></div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <?php if($stats['disputed_projects'] > 0): ?>
    <div class="p-4 bg-red-900/30 border border-red-500/40 rounded-xl flex items-center gap-4">
        <div class="w-10 h-10 bg-red-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-red-400"></i>
        </div>
        <div class="flex-1">
            <p class="text-sm font-bold text-red-300"><?php echo e($stats['disputed_projects']); ?> Proyek dalam Sengketa!</p>
            <p class="text-xs text-red-400/70">Memerlukan tindakan admin segera.</p>
        </div>
        <a href="#disputes" class="btn-primary px-4 py-2 rounded-xl text-sm font-semibold flex-shrink-0 bg-red-500 hover:bg-red-600" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
            Tangani Sekarang
        </a>
    </div>
    <?php endif; ?>

    <div class="grid lg:grid-cols-2 gap-6">
        
        <div class="card overflow-hidden">
            <div class="p-5 border-b border-white/5">
                <h2 class="text-sm font-bold text-white">User Terbaru</h2>
            </div>
            <div class="divide-y divide-white/5">
                <?php $__empty_1 = true; $__currentLoopData = $recentUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center gap-3 px-5 py-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold
                        <?php echo e($u->role === 'mahasiswa' ? 'bg-primary-500/20 text-primary-400' : ($u->role === 'client' ? 'bg-accent-500/20 text-accent-400' : 'bg-yellow-500/20 text-yellow-400')); ?>">
                        <?php echo e(strtoupper(substr($u->name, 0, 1))); ?>

                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate"><?php echo e($u->name); ?></p>
                        <p class="text-xs text-gray-600 truncate"><?php echo e($u->email); ?></p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="text-[10px] px-2 py-0.5 rounded-full
                            <?php echo e($u->role === 'mahasiswa' ? 'bg-primary-500/20 text-primary-400' : ($u->role === 'client' ? 'bg-accent-500/20 text-accent-400' : 'bg-yellow-500/20 text-yellow-400')); ?>">
                            <?php echo e(ucfirst($u->role)); ?>

                        </span>
                        <p class="text-[10px] text-gray-600 mt-0.5"><?php echo e($u->created_at->diffForHumans()); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="p-8 text-center text-sm text-gray-600">Tidak ada data</div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="card overflow-hidden">
            <div class="p-5 border-b border-white/5">
                <h2 class="text-sm font-bold text-white">Proyek Terkini</h2>
            </div>
            <div class="divide-y divide-white/5">
                <?php $__empty_1 = true; $__currentLoopData = $recentProjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center gap-3 px-5 py-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate"><?php echo e($project->title); ?></p>
                        <p class="text-xs text-gray-600"><?php echo e($project->client?->user?->name ?? '—'); ?></p>
                    </div>
                    <?php
                        $badge = [
                            'open' => ['class' => 'badge-open', 'label' => 'Open'],
                            'waiting_payment' => ['class' => 'badge-waiting', 'label' => 'Bayar'],
                            'in_progress' => ['class' => 'badge-progress', 'label' => 'Progress'],
                            'in_review' => ['class' => 'badge-review', 'label' => 'Review'],
                            'completed' => ['class' => 'badge-completed', 'label' => 'Selesai'],
                            'disputed' => ['class' => 'badge-disputed', 'label' => 'Sengketa'],
                        ][$project->status] ?? ['class' => 'badge-open', 'label' => $project->status];
                    ?>
                    <span class="badge <?php echo e($badge['class']); ?> flex-shrink-0"><?php echo e($badge['label']); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="p-8 text-center text-sm text-gray-600">Tidak ada data</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <?php if($disputedProjects->isNotEmpty()): ?>
    <div class="card overflow-hidden" id="disputes">
        <div class="p-5 border-b border-red-500/20 bg-red-900/10">
            <h2 class="text-sm font-bold text-red-300 flex items-center gap-2">
                <i class="fas fa-flag text-red-400"></i> Proyek dalam Sengketa — Perlu Tindakan Admin
            </h2>
        </div>
        <div class="divide-y divide-white/5">
            <?php $__currentLoopData = $disputedProjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="p-5">
                <div class="flex items-start gap-4">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-white"><?php echo e($project->title); ?></h3>
                        <div class="flex gap-4 mt-1">
                            <p class="text-xs text-gray-500">Client: <span class="text-gray-400"><?php echo e($project->client?->user?->name); ?></span></p>
                            <p class="text-xs text-gray-500">Mahasiswa: <span class="text-gray-400"><?php echo e($project->selectedStudent?->user?->name ?? '—'); ?></span></p>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">Budget: Rp <?php echo e(number_format($project->agreed_budget, 0, ',', '.')); ?></p>
                    </div>
                    <div class="flex gap-2 flex-shrink-0">
                        <form action="<?php echo e(route('admin.resolve-dispute', [$project->id, 'release'])); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn-success px-4 py-2 rounded-xl text-xs font-bold"
                                onclick="return confirm('Cairkan dana ke mahasiswa?')">
                                <i class="fas fa-check mr-1"></i>Cairkan ke Mahasiswa
                            </button>
                        </form>
                        <form action="<?php echo e(route('admin.resolve-dispute', [$project->id, 'refund'])); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-red-400 border border-red-500/30 hover:bg-red-500/10 transition-colors"
                                onclick="return confirm('Kembalikan dana ke client?')">
                                <i class="fas fa-undo mr-1"></i>Refund ke Client
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="card overflow-hidden">
        <div class="p-5 border-b border-white/5">
            <h2 class="text-sm font-bold text-white">Transaksi Terkini</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Proyek</th>
                        <th class="text-left">Client</th>
                        <th class="text-left">Jumlah</th>
                        <th class="text-left">Platform Fee</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $recentPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="text-sm text-white"><?php echo e(Str::limit($payment->project?->title, 30)); ?></td>
                        <td class="text-sm text-gray-300"><?php echo e($payment->client?->user?->name); ?></td>
                        <td class="text-sm font-bold text-white">Rp <?php echo e(number_format($payment->amount, 0, ',', '.')); ?></td>
                        <td class="text-sm text-accent-400">Rp <?php echo e(number_format($payment->platform_fee, 0, ',', '.')); ?></td>
                        <td>
                            <?php
                                $badge = [
                                    'pending' => ['class' => 'badge-waiting', 'label' => 'Pending'],
                                    'held' => ['class' => 'badge-progress', 'label' => 'Ditahan'],
                                    'released' => ['class' => 'badge-completed', 'label' => 'Cair'],
                                    'refunded' => ['class' => 'badge-revision', 'label' => 'Refund'],
                                ][$payment->status] ?? ['class' => 'badge-open', 'label' => $payment->status];
                            ?>
                            <span class="badge <?php echo e($badge['class']); ?>"><?php echo e($badge['label']); ?></span>
                        </td>
                        <td class="text-xs text-gray-500"><?php echo e($payment->created_at->format('d M Y')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="text-center py-8 text-gray-600">Tidak ada transaksi</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\SkillSync\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>