<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Exportação de Processos</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #222; }
        h1 { margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; vertical-align: top; }
        th { background: #f3f3f3; }
    </style>
</head>
<body onload="window.print()">
    <h1>Relatório de Processos</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Número</th>
                <th>Tipo</th>
                <th>Status</th>
                <th>Responsável</th>
                <th>Clientes</th>
                <th>Filiais</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $processos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $processo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($processo->id); ?></td>
                    <td><?php echo e($processo->numero_processo); ?></td>
                    <td><?php echo e($processo->tipoAcao?->nome ?? ($processo->tipo_acao ?? '-')); ?></td>
                    <td><?php echo e(ucfirst($processo->status)); ?></td>
                    <td><?php echo e($processo->responsavel->name ?? '-'); ?></td>
                    <td><?php echo e($processo->clientes->pluck('nome')->implode(', ')); ?></td>
                    <td><?php echo e($processo->filiais->pluck('nome')->implode(', ')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH C:\juridico\resources\views/exports/processos-print.blade.php ENDPATH**/ ?>