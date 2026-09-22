<?php $__env->startSection('title', env('APP_NAME')); ?>

<?php $__env->startSection('adminlte_css'); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/adminlte-custom.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content_top_nav_left'); ?>
    <?php echo $__env->make('layouts.navbar_left', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php if(isset($tela) && $tela == 'pesquisa'): ?>
    <?php $__env->startSection('content_header'); ?>
        <div class="form-group row">
            <h1 class="m-0 text-dark col-sm-11 col-form-label">Contas a Pagar</h1>
            <div class="col-sm-1">
                <?php echo $__env->make('layouts.nav-open-incluir', ['rotaIncluir' => $rotaIncluir], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
    <?php $__env->stopSection(); ?>

    <?php $__env->startSection('content'); ?>
    
        <div class="right_col" role="main">
            <?php if(session('success')): ?>
                <div class="alert alert-success"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
            <?php endif; ?>

            <div class="mb-3">
                <a href="<?php echo e(route('relatorios.fluxo-caixa')); ?>" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-chart-line"></i> Fluxo de Caixa
                </a>
                <a href="<?php echo e(route('relatorios.inadimplencia')); ?>" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-exclamation-triangle"></i> Inadimplência
                </a>
            </div>

            <form id="filtro" action="<?php echo e(route('despesas')); ?>" method="get" class="form-horizontal form-label-left" novalidate>
                <div class="form-group row">
                    <label for="descricao" class="col-sm-1 col-form-label text-right">Descrição</label>
                    <div class="col-sm-3">
                        <input type="text" id="descricao" name="descricao" class="form-control" value="<?php echo e($request->input('descricao') ?? ''); ?>">
                    </div>
                    <label for="status" class="col-sm-1 col-form-label text-right">Status</label>
                    <div class="col-sm-2">
                        <select class="form-control" id="status" name="status">
                            <option value="">Todos</option>
                            <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $valor => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($valor); ?>" <?php echo e($request->input('status') === $valor ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-sm-1">
                        <button type="submit" class="btn btn-primary">Pesquisar</button>
                    </div>
                </div>
            </form>

            <div class="x_panel">
                <div class="x_title">
                    <h4>Encontradas</h4>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table class="table table-striped text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Descrição</th>
                                <th>Categoria</th>
                                <th>Filial</th>
                                <th>Valor</th>
                                <th>Vencimento</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $despesas ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $despesa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <th scope="row"><a href="<?php echo e(route($rotaAlterar, ['id' => $despesa->id])); ?>"><?php echo e($despesa->id); ?></a></th>
                                    <td><?php echo e($despesa->descricao); ?></td>
                                    <td><?php echo e($despesa->categoria ?? '-'); ?></td>
                                    <td><?php echo e($despesa->filial->nome ?? '-'); ?></td>
                                    <td>R$ <?php echo e(number_format($despesa->valor, 2, ',', '.')); ?></td>
                                    <td><?php echo e($despesa->data_vencimento->format('d/m/Y')); ?></td>
                                    <td>
                                        <?php
                                            $badgeClass = match($despesa->computed_status) {
                                                'pago' => 'badge-primary',
                                                'atrasado' => 'badge-danger',
                                                default => 'badge-warning',
                                            };
                                        ?>
                                        <span class="badge <?php echo e($badgeClass); ?>"><?php echo e($despesa->computed_status_label); ?></span>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route($rotaAlterar, ['id' => $despesa->id])); ?>" class="btn btn-link btn-sm">Editar</a>
                                        <?php if($despesa->computed_status !== 'pago'): ?>
                                            <form action="<?php echo e(route('despesas.pagar')); ?>" method="post" style="display:inline;">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="id" value="<?php echo e($despesa->id); ?>">
                                                <button type="submit" class="btn btn-link btn-sm text-success">Pagar</button>
                                            </form>
                                        <?php endif; ?>
                                        <form action="<?php echo e(route('excluir-despesas')); ?>" method="post" style="display:inline;">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="id" value="<?php echo e($despesa->id); ?>">
                                            <button type="submit" class="btn btn-link btn-sm text-danger" onclick="return confirm('Deseja realmente excluir esta despesa?')">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8">Nenhuma despesa encontrada.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php $__env->stopSection(); ?>
<?php else: ?>
    <?php $__env->startSection('content_header'); ?>
        <h1 class="m-0 text-dark"><?php echo e($tela == 'alterar' ? 'Alteração de' : 'Inclusão de'); ?> Despesa</h1>
    <?php $__env->stopSection(); ?>

    <?php $__env->startSection('content'); ?>
    
        <div class="right_col" role="main">
            <form action="<?php echo e($tela == 'alterar' ? route($rotaAlterar) : route($rotaIncluir)); ?>" method="post">
                <?php echo csrf_field(); ?>
                <?php if($tela == 'alterar'): ?>
                    <input type="hidden" name="id" value="<?php echo e($despesa->id ?? ''); ?>">
                <?php endif; ?>

                <div class="container-fluid">
                    <div class="row row-cols-md-2 g-3 mt-2">
                        <div class="col-md-6">
                            <label for="descricao" class="form-label">Descrição <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php $__errorArgs = ['descricao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="descricao" name="descricao" value="<?php echo e(old('descricao', $despesa->descricao ?? '')); ?>">
                            <?php $__errorArgs = ['descricao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label for="categoria" class="form-label">Categoria</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['categoria'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="categoria" name="categoria" value="<?php echo e(old('categoria', $despesa->categoria ?? '')); ?>" placeholder="Ex: Aluguel, Folha, Fornecedores...">
                            <?php $__errorArgs = ['categoria'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="row row-cols-md-3 g-3 mt-2">
                        <div class="col-md-4">
                            <label for="valor" class="form-label">Valor <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control <?php $__errorArgs = ['valor'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="valor" name="valor" value="<?php echo e(old('valor', $despesa->valor ?? '')); ?>">
                            <?php $__errorArgs = ['valor'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-4">
                            <label for="data_vencimento" class="form-label">Data de vencimento <span class="text-danger">*</span></label>
                            <input type="date" class="form-control <?php $__errorArgs = ['data_vencimento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="data_vencimento" name="data_vencimento" value="<?php echo e(old('data_vencimento', isset($despesa) && $despesa->data_vencimento ? $despesa->data_vencimento->format('Y-m-d') : '')); ?>">
                            <?php $__errorArgs = ['data_vencimento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-4">
                            <label for="filial_id" class="form-label">Filial</label>
                            <select class="form-control <?php $__errorArgs = ['filial_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="filial_id" name="filial_id">
                                <option value="">Nenhuma</option>
                                <?php $__currentLoopData = $filiaisOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $nome): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($id); ?>" <?php echo e((string) old('filial_id', $despesa->filial_id ?? '') === (string) $id ? 'selected' : ''); ?>><?php echo e($nome); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['filial_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <?php if(isset($despesa)): ?>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label class="form-label">Status atual</label>
                                <div>
                                    <?php
                                        $badgeClass = match($despesa->computed_status) {
                                            'pago' => 'badge-primary',
                                            'atrasado' => 'badge-danger',
                                            default => 'badge-warning',
                                        };
                                    ?>
                                    <span class="badge <?php echo e($badgeClass); ?>"><?php echo e($despesa->computed_status_label); ?></span>
                                    <?php if($despesa->data_pagamento): ?>
                                        <small class="text-muted ms-2">Pago em <?php echo e($despesa->data_pagamento->format('d/m/Y')); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row row-cols-md-3 g-3 mt-2">
                        <div class="col-md-12">
                            <label for="observacoes" class="form-label">Observações</label>
                            <textarea class="form-control <?php $__errorArgs = ['observacoes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="observacoes" name="observacoes" rows="4"><?php echo e(old('observacoes', $despesa->observacoes ?? '')); ?></textarea>
                            <?php $__errorArgs = ['observacoes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-sm-5">
                            <button class="btn btn-danger" onclick="window.history.back();" type="button">Cancelar</button>
                        </div>
                        <div class="col-sm-5">
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    <?php $__env->stopSection(); ?>
<?php endif; ?>

<?php echo $__env->make('layouts.extra-content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('layouts.extra-content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/despesas.blade.php ENDPATH**/ ?>