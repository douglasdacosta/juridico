<?php $__env->startSection('title', env('APP_NAME')); ?>

<?php $__env->startSection('adminlte_css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/adminlte-custom.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content_top_nav_left'); ?>
    <?php echo $__env->make('layouts.navbar_left', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content_header'); ?>
    <h1 class="m-0 text-dark">Inadimplência</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <div class="right_col" role="main">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="x_panel">
                    <div class="x_content text-center">
                        <small class="text-muted">Total a receber em atraso</small>
                        <h3 class="text-danger">R$ <?php echo e(number_format($totalReceberAtraso, 2, ',', '.')); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="x_panel">
                    <div class="x_content text-center">
                        <small class="text-muted">Total a pagar em atraso</small>
                        <h3 class="text-danger">R$ <?php echo e(number_format($totalPagarAtraso, 2, ',', '.')); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="x_panel">
            <div class="x_title">
                <h4>Itens vencidos (ordenados por dias de atraso)</h4>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <table class="table table-striped text-center">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Descrição</th>
                            <th>Valor</th>
                            <th>Vencimento</th>
                            <th>Dias em atraso</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $itens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($item['tipo']); ?></td>
                                <td><?php echo e($item['descricao']); ?></td>
                                <td>R$ <?php echo e(number_format($item['valor'], 2, ',', '.')); ?></td>
                                <td><?php echo e($item['vencimento']->format('d/m/Y')); ?></td>
                                <td><span class="badge badge-danger"><?php echo e($item['dias_atraso']); ?> dia(s)</span></td>
                                <td><a href="<?php echo e($item['link']); ?>" class="btn btn-link btn-sm">Ver</a></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6">Nenhum item em atraso. 🎉</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.extra-content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/relatorios/inadimplencia.blade.php ENDPATH**/ ?>