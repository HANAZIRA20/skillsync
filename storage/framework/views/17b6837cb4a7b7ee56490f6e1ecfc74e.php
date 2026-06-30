<?php $__env->startSection('title', 'Analytics OLAP'); ?>
<?php $__env->startSection('page-title', 'Analytics Dashboard'); ?>
<?php $__env->startSection('page-subtitle', 'Insight mendalam platform SkillSync dengan OLAP queries'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 animate-fade-in">

    
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card card p-5">
            <div class="w-10 h-10 bg-primary-500/20 rounded-xl flex items-center justify-center mb-3">
                <i class="fas fa-users text-primary-400"></i>
            </div>
            <div class="text-2xl font-black text-white"><?php echo e($studentAnalytics['totalStudents']); ?></div>
            <div class="text-xs text-gray-400 mt-0.5">Total Mahasiswa</div>
            <div class="text-[10px] text-gray-600 mt-0.5"><?php echo e($studentAnalytics['activeStudents']); ?> aktif (KRS uploaded)</div>
        </div>
        <div class="stat-card card p-5">
            <div class="w-10 h-10 bg-purple-500/20 rounded-xl flex items-center justify-center mb-3">
                <i class="fas fa-bullseye text-purple-400"></i>
            </div>
            <div class="text-2xl font-black text-white"><?php echo e($matchingAnalytics['totalMatches']); ?></div>
            <div class="text-xs text-gray-400 mt-0.5">Total AI Matchings</div>
            <div class="text-[10px] text-gray-600 mt-0.5"><?php echo e($matchingAnalytics['acceptanceRate']); ?>% acceptance rate</div>
        </div>
        <div class="stat-card card p-5">
            <div class="w-10 h-10 bg-yellow-500/20 rounded-xl flex items-center justify-center mb-3">
                <i class="fas fa-coins text-yellow-400"></i>
            </div>
            <div class="text-xl font-black text-white">Rp <?php echo e(number_format($revenueAnalytics['totalRevenue'], 0, ',', '.')); ?></div>
            <div class="text-xs text-gray-400 mt-0.5">Platform Revenue</div>
            <div class="text-[10px] text-gray-600 mt-0.5"><?php echo e($revenueAnalytics['successfulPayments']); ?> transaksi sukses</div>
        </div>
        <div class="stat-card card p-5">
            <div class="w-10 h-10 bg-accent-500/20 rounded-xl flex items-center justify-center mb-3">
                <i class="fas fa-briefcase text-accent-400"></i>
            </div>
            <div class="text-2xl font-black text-white"><?php echo e($projectAnalytics['totalProjects']); ?></div>
            <div class="text-xs text-gray-400 mt-0.5">Total Proyek</div>
            <div class="text-[10px] text-gray-600 mt-0.5"><?php echo e($projectAnalytics['completedProjects']); ?> selesai</div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        
        <div class="card p-6">
            <h2 class="text-sm font-bold text-white mb-5 flex items-center gap-2">
                <i class="fas fa-chart-pie text-primary-400"></i> Distribusi Status Proyek
            </h2>
            <?php
                $statusColors = [
                    'open' => ['bar' => 'bg-accent-500', 'label' => 'Open'],
                    'waiting_payment' => ['bar' => 'bg-yellow-500', 'label' => 'Menunggu Bayar'],
                    'in_progress' => ['bar' => 'bg-primary-500', 'label' => 'In Progress'],
                    'in_review' => ['bar' => 'bg-orange-500', 'label' => 'Review'],
                    'revision' => ['bar' => 'bg-red-400', 'label' => 'Revisi'],
                    'completed' => ['bar' => 'bg-green-500', 'label' => 'Selesai'],
                    'cancelled' => ['bar' => 'bg-gray-600', 'label' => 'Dibatalkan'],
                    'disputed' => ['bar' => 'bg-red-600', 'label' => 'Sengketa'],
                ];
                $totalP = array_sum($projectAnalytics['statusDistribution']);
            ?>
            <div class="space-y-3">
                <?php $__currentLoopData = $projectAnalytics['statusDistribution']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $config = $statusColors[$status] ?? ['bar' => 'bg-gray-500', 'label' => $status];
                    $pct = $totalP > 0 ? round(($count / $totalP) * 100) : 0;
                ?>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-gray-400"><?php echo e($config['label']); ?></span>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-white"><?php echo e($count); ?></span>
                            <span class="text-[10px] text-gray-600">(<?php echo e($pct); ?>%)</span>
                        </div>
                    </div>
                    <div class="progress-bar h-2">
                        <div class="progress-fill <?php echo e($config['bar']); ?>" style="width: <?php echo e($pct); ?>%"></div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
        <div class="card p-6">
            <h2 class="text-sm font-bold text-white mb-5 flex items-center gap-2">
                <i class="fas fa-robot text-purple-400"></i> Distribusi AI Score
            </h2>
            <div class="space-y-3 mb-5">
                <?php
                    $scoreColors = ['90-100' => 'bg-accent-500', '75-89' => 'bg-primary-500', '60-74' => 'bg-yellow-500', '40-59' => 'bg-orange-500', '0-39' => 'bg-red-500'];
                    $totalM = array_sum($matchingAnalytics['scoreDistribution']);
                ?>
                <?php $__currentLoopData = $matchingAnalytics['scoreDistribution']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $range => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $pct = $totalM > 0 ? round(($count / $totalM) * 100) : 0; ?>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-gray-400">Score <?php echo e($range); ?></span>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-white"><?php echo e($count); ?></span>
                            <span class="text-[10px] text-gray-600">(<?php echo e($pct); ?>%)</span>
                        </div>
                    </div>
                    <div class="progress-bar h-2">
                        <div class="progress-fill <?php echo e($scoreColors[$range] ?? 'bg-gray-500'); ?>" style="width: <?php echo e($pct); ?>%"></div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="grid grid-cols-2 gap-3 pt-3 border-t border-white/5">
                <div class="text-center">
                    <div class="text-xl font-black text-primary-400"><?php echo e(round($matchingAnalytics['avgScore'] ?? 0, 1)); ?>%</div>
                    <div class="text-xs text-gray-500">Rata-rata Score</div>
                </div>
                <div class="text-center">
                    <div class="text-xl font-black text-accent-400"><?php echo e($matchingAnalytics['acceptanceRate']); ?>%</div>
                    <div class="text-xs text-gray-500">Acceptance Rate</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        
        <div class="card p-6">
            <h2 class="text-sm font-bold text-white mb-5 flex items-center gap-2">
                <i class="fas fa-tags text-accent-400"></i> Kategori Proyek Terpopuler
            </h2>
            <?php if($projectAnalytics['categoryPopularity']->isEmpty()): ?>
            <div class="text-center py-8 text-gray-600 text-sm">Belum ada data</div>
            <?php else: ?>
            <?php $maxCat = $projectAnalytics['categoryPopularity']->max('count') ?: 1; ?>
            <div class="space-y-3">
                <?php $__currentLoopData = $projectAnalytics['categoryPopularity']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-gray-400"><?php echo e($cat->category); ?></span>
                        <span class="text-xs font-bold text-white"><?php echo e($cat->count); ?></span>
                    </div>
                    <div class="progress-bar h-1.5">
                        <div class="progress-fill bg-gradient-to-r from-accent-600 to-accent-400"
                            style="width: <?php echo e(round(($cat->count / $maxCat) * 100)); ?>%"></div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
        </div>

        
        <div class="card p-6">
            <h2 class="text-sm font-bold text-white mb-5 flex items-center gap-2">
                <i class="fas fa-brain text-yellow-400"></i> Skill Paling Dicari
            </h2>
            <?php if(empty($matchingAnalytics['topSkills'])): ?>
            <div class="text-center py-8 text-gray-600 text-sm">Belum ada data skill</div>
            <?php else: ?>
            <?php $maxSkill = max($matchingAnalytics['topSkills']); ?>
            <div class="space-y-3">
                <?php $__currentLoopData = $matchingAnalytics['topSkills']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $skill => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-gray-400"><?php echo e($skill); ?></span>
                        <span class="text-xs font-bold text-white"><?php echo e($count); ?></span>
                    </div>
                    <div class="progress-bar h-1.5">
                        <div class="progress-fill bg-gradient-to-r from-yellow-600 to-yellow-400"
                            style="width: <?php echo e(round(($count / $maxSkill) * 100)); ?>%"></div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        
        <div class="card p-6">
            <h2 class="text-sm font-bold text-white mb-5 flex items-center gap-2">
                <i class="fas fa-chart-bar text-green-400"></i> Revenue per Kategori
            </h2>
            <?php if($revenueAnalytics['revenueByCategory']->isEmpty()): ?>
            <div class="text-center py-8 text-gray-600 text-sm">Belum ada data transaksi</div>
            <?php else: ?>
            <?php $maxRev = $revenueAnalytics['revenueByCategory']->max('revenue') ?: 1; ?>
            <div class="space-y-3">
                <?php $__currentLoopData = $revenueAnalytics['revenueByCategory']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-gray-400"><?php echo e($rev->project_category); ?></span>
                        <span class="text-xs font-bold text-accent-400">Rp <?php echo e(number_format($rev->revenue, 0, ',', '.')); ?></span>
                    </div>
                    <div class="progress-bar h-1.5">
                        <div class="progress-fill bg-gradient-to-r from-green-600 to-accent-500"
                            style="width: <?php echo e(round(($rev->revenue / $maxRev) * 100)); ?>%"></div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
        </div>

        
        <div class="card p-6">
            <h2 class="text-sm font-bold text-white mb-5 flex items-center gap-2">
                <i class="fas fa-building text-blue-400"></i> Industri Client
            </h2>
            <?php if($clientAnalytics['industryDistribution']->isEmpty()): ?>
            <div class="text-center py-8 text-gray-600 text-sm">Belum ada data</div>
            <?php else: ?>
            <?php $maxInd = $clientAnalytics['industryDistribution']->max('count') ?: 1; ?>
            <div class="space-y-3">
                <?php $__currentLoopData = $clientAnalytics['industryDistribution']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ind): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-gray-400"><?php echo e($ind->industry); ?></span>
                        <span class="text-xs font-bold text-white"><?php echo e($ind->count); ?></span>
                    </div>
                    <div class="progress-bar h-1.5">
                        <div class="progress-fill bg-gradient-to-r from-blue-600 to-blue-400"
                            style="width: <?php echo e(round(($ind->count / $maxInd) * 100)); ?>%"></div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <?php if($studentAnalytics['topStudents']->isNotEmpty()): ?>
    <div class="card overflow-hidden">
        <div class="p-5 border-b border-white/5">
            <h2 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fas fa-trophy text-yellow-400"></i> Top Mahasiswa Berdasarkan Proyek
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">#</th>
                        <th class="text-left">Mahasiswa</th>
                        <th class="text-left">Universitas</th>
                        <th class="text-left">Total Proyek</th>
                        <th class="text-left">Total Penghasilan</th>
                        <th class="text-left">Avg Rating</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $studentAnalytics['topStudents']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <span class="<?php echo e($index < 3 ? 'text-yellow-400 font-black' : 'text-gray-500'); ?>">
                                <?php echo e($index < 3 ? ['🥇','🥈','🥉'][$index] : ($index + 1)); ?>

                            </span>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-primary-500/20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-primary-400 text-xs"></i>
                                </div>
                                <span class="text-sm text-white">ID #<?php echo e($student->student_id); ?></span>
                            </div>
                        </td>
                        <td class="text-sm text-gray-400"><?php echo e($student->student_universitas ?? '—'); ?></td>
                        <td class="text-sm font-bold text-white"><?php echo e($student->total_projects); ?></td>
                        <td class="text-sm font-bold text-accent-400">Rp <?php echo e(number_format($student->total_earned, 0, ',', '.')); ?></td>
                        <td>
                            <div class="flex items-center gap-1">
                                <i class="fas fa-star text-yellow-400 text-xs"></i>
                                <span class="text-sm text-white"><?php echo e(number_format($student->avg_rating ?? 0, 1)); ?></span>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="grid lg:grid-cols-3 gap-4">
        <?php $__currentLoopData = [
            ['label' => 'Dana Ditahan Escrow', 'value' => 'Rp ' . number_format($revenueAnalytics['heldAmount'], 0, ',', '.'), 'icon' => 'fa-lock', 'color' => 'primary'],
            ['label' => 'Total Refund', 'value' => 'Rp ' . number_format($revenueAnalytics['totalRefunds'], 0, ',', '.'), 'icon' => 'fa-undo', 'color' => 'red', 'sub' => $revenueAnalytics['refundCount'] . 'x transaksi'],
            ['label' => 'Avg Durasi Proyek', 'value' => round($projectAnalytics['avgDuration'] ?? 0) . ' hari', 'icon' => 'fa-calendar', 'color' => 'accent'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="card p-5">
            <div class="w-10 h-10 bg-<?php echo e($card['color']); ?>-500/20 rounded-xl flex items-center justify-center mb-3">
                <i class="fas <?php echo e($card['icon']); ?> text-<?php echo e($card['color']); ?>-400"></i>
            </div>
            <div class="text-xl font-black text-white"><?php echo e($card['value']); ?></div>
            <div class="text-xs text-gray-400 mt-0.5"><?php echo e($card['label']); ?></div>
            <?php if(isset($card['sub'])): ?>
            <div class="text-[10px] text-gray-600 mt-0.5"><?php echo e($card['sub']); ?></div>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\SkillSync\resources\views/analytics/dashboard.blade.php ENDPATH**/ ?>