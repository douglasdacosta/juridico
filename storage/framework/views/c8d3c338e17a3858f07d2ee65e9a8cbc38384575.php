<?php $__env->startSection('title', env('APP_NAME')); ?>

<?php $__env->startSection('adminlte_css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/adminlte-custom.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content_top_nav_left'); ?>
    <?php echo $__env->make('layouts.navbar_left', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content_header'); ?>
    <h1 class="m-0 text-dark">Fluxo de Caixa</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <div class="right_col" role="main">
        <form action="<?php echo e(route('relatorios.fluxo-caixa')); ?>" method="get" class="form-inline mb-3">
            <label for="meses" class="mr-2">Período</label>
            <select name="meses" id="meses" class="form-control mr-2" onchange="this.form.submit()">
                <?php $__currentLoopData = [3, 6, 12, 24]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opcao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($opcao); ?>" <?php echo e($meses == $opcao ? 'selected' : ''); ?>>Últimos <?php echo e($opcao); ?> meses</option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <a href="<?php echo e(route('relatorios.fluxo-caixa.pdf', ['meses' => $meses])); ?>" class="btn btn-outline-secondary" target="_blank">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
        </form>

        <div class="row mb-3">
            <div class="col-md-4">
                <div class="x_panel">
                    <div class="x_content text-center">
                        <small class="text-muted">Total de entradas</small>
                        <h3 class="text-success">R$ <?php echo e(number_format($totalEntradas, 2, ',', '.')); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="x_panel">
                    <div class="x_content text-center">
                        <small class="text-muted">Total de saídas</small>
                        <h3 class="text-danger">R$ <?php echo e(number_format($totalSaidas, 2, ',', '.')); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="x_panel">
                    <div class="x_content text-center">
                        <small class="text-muted">Saldo do período</small>
                        <h3 class="<?php echo e(($totalEntradas - $totalSaidas) >= 0 ? 'text-success' : 'text-danger'); ?>">
                            R$ <?php echo e(number_format($totalEntradas - $totalSaidas, 2, ',', '.')); ?>

                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="x_panel">
            <div class="x_title">
                <h4>Gráfico</h4>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <canvas id="graficoFluxoCaixa" height="90"></canvas>
            </div>
        </div>

        <div class="x_panel">
            <div class="x_content">
                <table class="table table-striped text-center">
                    <thead>
                        <tr>
                            <th>Mês</th>
                            <th>Entradas</th>
                            <th>Saídas</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $linhas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $linha): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($linha['mes']); ?></td>
                                <td class="text-success">R$ <?php echo e(number_format($linha['entradas'], 2, ',', '.')); ?></td>
                                <td class="text-danger">R$ <?php echo e(number_format($linha['saidas'], 2, ',', '.')); ?></td>
                                <td class="<?php echo e($linha['saldo'] >= 0 ? 'text-success' : 'text-danger'); ?>">
                                    R$ <?php echo e(number_format($linha['saldo'], 2, ',', '.')); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var linhas = <?php echo json_encode($linhas, 15, 512) ?>;
            var canvas = document.getElementById('graficoFluxoCaixa');
            if (canvas && typeof Chart !== 'undefined') {
                new Chart(canvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: linhas.map(function (l) { return l.mes; }),
                        datasets: [
                            { label: 'Entradas', data: linhas.map(function (l) { return l.entradas; }), backgroundColor: '#28a745' },
                            { label: 'Saídas', data: linhas.map(function (l) { return l.saidas; }), backgroundColor: '#dc3545' },
                        ],
                    },
                    options: { plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } },
                });
            }
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.extra-content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/relatorios/fluxo-caixa.blade.php ENDPATH**/ ?>