<?php $__env->startSection('title', env('APP_NAME')); ?>

<?php $__env->startSection('adminlte_css'); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/adminlte-custom.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/select2.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content_top_nav_left'); ?>
    <?php echo $__env->make('layouts.navbar_left', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php if(isset($tela) && $tela == 'pesquisa'): ?>
    <?php $__env->startSection('content_header'); ?>
        <div class="form-group row">
            <h1 class="m-0 text-dark col-sm-11 col-form-label">Pesquisa de <?php echo e($nome_tela); ?></h1>
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

            <form id="filtro" action="<?php echo e(route('financeiro')); ?>" method="get" class="form-horizontal form-label-left" novalidate>
                <div class="form-group row">
                    <label for="cliente_id" class="col-sm-1 col-form-label text-right">Cliente</label>
                    <div class="col-sm-3">
                        <select class="form-control select2-ajax-clientes" id="cliente_id" name="cliente_id" style="width: 100%;">
                            <option value="">Todos</option>
                            <?php if($request->filled('cliente_id')): ?>
                                <?php
                                    $clienteSelecionado = \App\Models\Cliente::find($request->input('cliente_id'));
                                ?>
                                <?php if($clienteSelecionado): ?>
                                    <option value="<?php echo e($clienteSelecionado->id); ?>" selected><?php echo e($clienteSelecionado->nome); ?></option>
                                <?php endif; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <label for="numero_processo" class="col-sm-2 col-form-label text-right">Nº Processo</label>
                    <div class="col-sm-2">
                        <input type="text" id="numero_processo" name="numero_processo" class="form-control" value="<?php echo e($request->input('numero_processo') ?? ''); ?>">
                    </div>
                    <label for="status_pagamento" class="col-sm-1 col-form-label text-right">Status</label>
                    <div class="col-sm-2">
                        <select class="form-control" id="status_pagamento" name="status_pagamento">
                            <option value="">Todos</option>
                            <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $valor => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($valor); ?>" <?php echo e($request->input('status_pagamento') === $valor ? 'selected' : ''); ?>><?php echo e($label); ?></option>
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
                    <h4>Encontrados</h4>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table class="table table-striped text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Processos</th>
                                <th>Valor da Causa</th>
                                <th>Honorários</th>
                                <th>Reembolso</th>
                                <th>Data Pagamento</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $lancamentos ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lancamento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <th scope="row"><a href="<?php echo e(route($rotaAlterar, ['id' => $lancamento->id])); ?>"><?php echo e($lancamento->id); ?></a></th>
                                    <td><?php echo e($lancamento->cliente->nome ?? '-'); ?></td>
                                    <td><?php echo e($lancamento->processos->pluck('numero_processo')->implode(', ')); ?></td>
                                    <td>R$ <?php echo e(number_format($lancamento->valor_causa, 2, ',', '.')); ?></td>
                                    <td><?php echo e($lancamento->honorarios !== null ? 'R$ ' . number_format($lancamento->honorarios, 2, ',', '.') : '-'); ?></td>
                                    <td><?php echo e($lancamento->reembolso !== null ? 'R$ ' . number_format($lancamento->reembolso, 2, ',', '.') : '-'); ?></td>
                                    <td><?php echo e($lancamento->data_pagamento?->format('d/m/Y') ?? '-'); ?></td>
                                    <td>
                                            <?php
                                                $computed = $lancamento->computed_status;
                                                $badgeClass = match($computed) {
                                                    'pago' => 'badge-primary',
                                                    'vencido' => 'badge-danger',
                                                    'em_dia' => 'badge-success',
                                                    'nao_pago' => 'badge-warning',
                                                    default => 'badge-secondary',
                                                };
                                            ?>
                                            <span class="badge <?php echo e($badgeClass); ?>"><?php echo e($lancamento->computed_status_label); ?></span>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route($rotaAlterar, ['id' => $lancamento->id])); ?>" class="btn btn-link btn-sm">Editar</a>
                                        <form action="<?php echo e(route('excluir-financeiro')); ?>" method="post" style="display:inline;">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="id" value="<?php echo e($lancamento->id); ?>">
                                            <button type="submit" class="btn btn-link btn-sm text-danger" onclick="return confirm('Deseja realmente excluir este lançamento?')">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="9">Nenhum lançamento financeiro encontrado.</td>
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
        <h1 class="m-0 text-dark"><?php echo e($tela == 'alterar' ? 'Alteração de' : 'Inclusão de'); ?> <?php echo e($nome_tela); ?></h1>
    <?php $__env->stopSection(); ?>

    <?php $__env->startSection('content'); ?>
    
        <div class="right_col" role="main">
            <form action="<?php echo e($tela == 'alterar' ? route($rotaAlterar) : route($rotaIncluir)); ?>" method="post">
                <?php echo csrf_field(); ?>
                <?php if($tela == 'alterar'): ?>
                    <input type="hidden" name="id" value="<?php echo e($financeiro->id ?? ''); ?>">
                <?php endif; ?>

                <div class="container-fluid">
                    <div class="row row-cols-md-2 g-3 mt-2">
                        <div class="col-md-6">
                            <label for="cliente_id" class="form-label">Cliente <span class="text-danger">*</span></label>
                            <select class="form-control select2-ajax-clientes <?php $__errorArgs = ['cliente_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="cliente_id" name="cliente_id" style="width: 100%;">
                                <option value="">Selecione</option>
                                <?php
                                    $clienteAtualId = old('cliente_id', $financeiro->cliente_id ?? '');
                                    $clienteAtual = $clienteAtualId ? \App\Models\Cliente::find($clienteAtualId) : null;
                                ?>
                                <?php if($clienteAtual): ?>
                                    <option value="<?php echo e($clienteAtual->id); ?>" selected><?php echo e($clienteAtual->nome); ?></option>
                                <?php endif; ?>
                            </select>
                            <?php $__errorArgs = ['cliente_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label for="processos" class="form-label">Processos vinculados <span class="text-danger">*</span></label>
                            <select multiple class="form-control select2-ajax-processos-multiple <?php $__errorArgs = ['processos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="processos" name="processos[]" style="width: 100%;" <?php echo e($clienteAtual ? '' : 'disabled'); ?>>
                                <?php if(old('processos')): ?>
                                    <?php
                                        $processosOld = \App\Models\Processo::whereIn('id', old('processos'))->get();
                                    ?>
                                    <?php $__currentLoopData = $processosOld; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $processo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($processo->id); ?>" selected><?php echo e($processo->numero_processo); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php elseif(isset($financeiro) && $financeiro->processos): ?>
                                    <?php $__currentLoopData = $financeiro->processos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $processo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($processo->id); ?>" selected><?php echo e($processo->numero_processo); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                            <small class="form-text text-muted">Selecione primeiro o cliente para listar os processos disponíveis.</small>
                            <?php $__errorArgs = ['processos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <?php if(isset($financeiro) && $financeiro->parcelas && $financeiro->parcelas->count()): ?>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <h5>Parcelas</h5>
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Parcela</th>
                                            <th>Vencimento</th>
                                            <th>Valor</th>
                                            <th>Pagamento</th>
                                            <th>Data pagamento</th>
                                            <th>Valor pago</th>
                                            <th>Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $financeiro->parcelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parcela): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr id="parcela-row-<?php echo e($parcela->id); ?>">
                                                <td><?php echo e($parcela->numero); ?></td>
                                                <td><?php echo e($parcela->data_vencimento?->format('d/m/Y')); ?></td>
                                                <td>R$ <?php echo e(number_format($parcela->valor, 2, ',', '.')); ?></td>
                                                <td class="parcela-status-<?php echo e($parcela->id); ?>">
                                                    <?php if($parcela->data_pagamento): ?>
                                                        Pago
                                                    <?php else: ?>
                                                        Pendente
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm mask-date parcela-data" data-parcela-id="<?php echo e($parcela->id); ?>" value="<?php echo e($parcela->data_pagamento ? $parcela->data_pagamento->format('d/m/Y') : ''); ?>">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm mask-money parcela-valor" data-parcela-id="<?php echo e($parcela->id); ?>" value="<?php echo e($parcela->valor_pago ? number_format($parcela->valor_pago, 2, ',', '.') : ''); ?>">
                                                </td>
                                                <td>
                                                    <?php if($parcela->status !== 'pago'): ?>
                                                        <button data-id="<?php echo e($parcela->id); ?>" class="btn btn-sm btn-success btn-pagar-parcela">Salvar pagamento</button>
                                                    <?php else: ?>
                                                        <span class="text-muted">Pago</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                    </div>

                    <div class="row row-cols-md-3 g-3 mt-2">
                        <div class="col-md-4">
                            <label for="valor_causa" class="form-label">Valor da causa <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control <?php $__errorArgs = ['valor_causa'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="valor_causa" name="valor_causa" value="<?php echo e(old('valor_causa', $financeiro->valor_causa ?? '')); ?>">
                            <?php $__errorArgs = ['valor_causa'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-4">
                            <label for="honorarios" class="form-label">Honorários</label>
                            <input type="number" step="0.01" min="0" class="form-control <?php $__errorArgs = ['honorarios'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="honorarios" name="honorarios" value="<?php echo e(old('honorarios', $financeiro->honorarios ?? '')); ?>">
                            <?php $__errorArgs = ['honorarios'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-4">
                            <label for="reembolso" class="form-label">Reembolso</label>
                            <input type="number" step="0.01" min="0" class="form-control <?php $__errorArgs = ['reembolso'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="reembolso" name="reembolso" value="<?php echo e(old('reembolso', $financeiro->reembolso ?? '')); ?>">
                            <?php $__errorArgs = ['reembolso'];
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
                        <div class="col-md-3">
                            <label for="data_pagamento" class="form-label">Data de pagamento</label>
                            <input type="text" class="form-control mask-date <?php $__errorArgs = ['data_pagamento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="data_pagamento" name="data_pagamento" value="<?php echo e(old('data_pagamento', isset($financeiro) && $financeiro->data_pagamento ? $financeiro->data_pagamento->format('d/m/Y') : '')); ?>">
                            <?php $__errorArgs = ['data_pagamento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-3">
                            <label for="valor_pago" class="form-label">Valor pago</label>
                            <input type="text" class="form-control mask-money <?php $__errorArgs = ['valor_pago'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="valor_pago" name="valor_pago" value="<?php echo e(old('valor_pago', isset($financeiro) && $financeiro->valor_pago ? number_format($financeiro->valor_pago, 2, ',', '.') : '')); ?>">
                            <?php $__errorArgs = ['valor_pago'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <?php if(isset($financeiro) && !($financeiro->parcelado ?? false) && ($financeiro->computed_status ?? '') !== 'pago'): ?>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-success btn-pagar-financeiro" data-id="<?php echo e($financeiro->id); ?>">Marcar como pago (à vista)</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <?php
                                $computed = $financeiro->computed_status ?? 'em_dia';
                                $badgeClass = match($computed) {
                                    'pago' => 'badge-primary',
                                    'vencido' => 'badge-danger',
                                    'em_dia' => 'badge-success',
                                    'nao_pago' => 'badge-warning',
                                    default => 'badge-secondary',
                                };
                            ?>
                            <div>
                                <span class="badge <?php echo e($badgeClass); ?>"><?php echo e($financeiro->computed_status_label ?? ($statusOptions[$computed] ?? $computed)); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="row row-cols-md-3 g-3 mt-2">
                        <div class="col-md-2 form-check align-self-end">
                            <?php $parceladoChecked = old('parcelado', isset($financeiro) ? ($financeiro->parcelado ? '1' : '0') : '0'); ?>
                            <input class="form-check-input" type="checkbox" id="parcelado" name="parcelado" value="1" <?php echo e($parceladoChecked == '1' ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="parcelado">Parcelado</label>
                        </div>
                        <div class="col-md-3">
                            <label for="numero_parcelas" class="form-label">Número de parcelas</label>
                            <input type="number" min="1" class="form-control <?php $__errorArgs = ['numero_parcelas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="numero_parcelas" name="numero_parcelas" value="<?php echo e(old('numero_parcelas', $financeiro->numero_parcelas ?? '')); ?>">
                            <?php $__errorArgs = ['numero_parcelas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-3">
                            <label for="valor_parcela" class="form-label">Valor da parcela</label>
                            <input type="number" step="0.01" min="0" class="form-control <?php $__errorArgs = ['valor_parcela'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="valor_parcela" name="valor_parcela" value="<?php echo e(old('valor_parcela', $financeiro->valor_parcela ?? '')); ?>">
                            <?php $__errorArgs = ['valor_parcela'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-4">
                            <label for="data_primeira_parcela" class="form-label">Data primeira parcela</label>
                            <input type="date" class="form-control <?php $__errorArgs = ['data_primeira_parcela'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="data_primeira_parcela" name="data_primeira_parcela" value="<?php echo e(old('data_primeira_parcela', isset($financeiro) && $financeiro->data_primeira_parcela ? $financeiro->data_primeira_parcela->format('Y-m-d') : '')); ?>">
                            <?php $__errorArgs = ['data_primeira_parcela'];
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
                        <div class="col-md-12">
                            <label for="observacoes" class="form-label">Observações</label>
                            <textarea class="form-control <?php $__errorArgs = ['observacoes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="observacoes" name="observacoes" rows="4"><?php echo e(old('observacoes', $financeiro->observacoes ?? '')); ?></textarea>
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

<?php $__env->startSection('js'); ?>
    <script src="js/jquery.mask.js"></script>
    <script src="js/main_custom.js"></script>
    <script src="js/select2.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function initProcessosSelect(clienteId) {
            $('.select2-ajax-processos-multiple').select2({
                ajax: {
                    url: '<?php echo e(route("api.processos.search")); ?>',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page || 1,
                            cliente_id: clienteId
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results,
                            pagination: data.pagination
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0,
                placeholder: 'Digite para buscar processos do cliente',
                allowClear: true,
                multiple: true,
                language: {
                    inputTooShort: function() { return 'Digite para buscar'; },
                    noResults: function() { return 'Nenhum processo encontrado'; },
                    searching: function() { return 'Buscando...'; },
                    loadingMore: function() { return 'Carregando mais resultados...'; }
                }
            });
        }

        $(function () {
            // Select2 AJAX para cliente (single)
            if ($('.select2-ajax-clientes').length) {
                $('.select2-ajax-clientes').select2({
                    ajax: {
                        url: '/api/clientes/search',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term,
                                page: params.page || 1
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data.results,
                                pagination: data.pagination
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 0,
                    placeholder: 'Digite para buscar cliente',
                    allowClear: true,
                    language: {
                        inputTooShort: function() { return 'Digite para buscar'; },
                        noResults: function() { return 'Nenhum cliente encontrado'; },
                        searching: function() { return 'Buscando...'; },
                        loadingMore: function() { return 'Carregando mais resultados...'; }
                    }
                });
            }

            // Select2 AJAX para processos (multiple), dependente do cliente selecionado
            if ($('.select2-ajax-processos-multiple').length) {
                var clienteInicial = $('#cliente_id').val() || null;
                initProcessosSelect(clienteInicial);

                // Ao trocar o cliente, reinicia o select de processos e limpa a seleção anterior
                $('#cliente_id').on('change', function () {
                    var clienteId = $(this).val();

                    $('.select2-ajax-processos-multiple').val(null).trigger('change');
                    $('.select2-ajax-processos-multiple').prop('disabled', !clienteId);

                    if ($('.select2-ajax-processos-multiple').data('select2')) {
                        $('.select2-ajax-processos-multiple').select2('destroy');
                    }

                    initProcessosSelect(clienteId);
                });
            }

            // Toggle campos de parcelamento
            function toggleParcelamento() {
                var isParcelado = $('#parcelado').is(':checked');
                $('#numero_parcelas').prop('disabled', !isParcelado);
                $('#valor_parcela').prop('disabled', !isParcelado);
                $('#data_primeira_parcela').prop('disabled', !isParcelado);
            }

            $('#parcelado').on('change', function () {
                toggleParcelamento();
            });

            // Inicializa estado
            toggleParcelamento();
                // Inicializar máscaras
                if ($.fn.mask) {
                    $('.mask-date').mask('00/00/0000');
                    $('.mask-money').mask('#.##0,00', {reverse: true});
                }

                function dateDMYtoISO(dmy) {
                    if (!dmy) return null;
                    var parts = dmy.split('/');
                    if (parts.length !== 3) return null;
                    return parts[2] + '-' + parts[1] + '-' + parts[0];
                }

                function moneyMaskToFloat(masked) {
                    if (!masked) return null;
                    // 1.234.567,89 => 1234567.89
                    var num = masked.replace(/\./g, '').replace(/,/g, '.');
                    return num;
                }

            
                // Marcar parcela como paga (AJAX) com valor pago opcional
                $(document).on('click', '.btn-pagar-parcela', function (e) {
                    e.preventDefault();
                    var btn = $(this);
                    var parcelaId = btn.data('id');
                    var row = $('#parcela-row-' + parcelaId);
                    var dataPagamentoMask = row.find('.parcela-data').val();
                    var valorPagoMask = row.find('.parcela-valor').val();

                    var dataPagamento = dateDMYtoISO(dataPagamentoMask);
                    var valorPago = moneyMaskToFloat(valorPagoMask);

                    $.post('<?php echo e(route('financeiro.parcela.pagar')); ?>', { parcela_id: parcelaId, valor_pago: valorPago, data_pagamento: dataPagamento }, function (resp) {
                        if (resp.success) {
                            location.reload();
                        } else {
                            alert('Erro ao marcar parcela como paga');
                        }
                    }).fail(function () {
                        alert('Erro ao comunicar o servidor');
                    });
                });

            
                $(document).on('click', '.btn-pagar-financeiro', function (e) {
                    e.preventDefault();
                    var btn = $(this);
                    var financeiroId = btn.data('id');
                    var dataPagamentoMask = $('#data_pagamento').val();
                    var valorPagoMask = $('#valor_pago').val();

                    var dataPagamento = dateDMYtoISO(dataPagamentoMask);
                    var valorPago = moneyMaskToFloat(valorPagoMask);

                    $.post('<?php echo e(route('financeiro.pagar')); ?>', { financeiro_id: financeiroId, valor_pago: valorPago, data_pagamento: dataPagamento }, function (resp) {
                        if (resp.success) {
                            location.reload();
                        } else {
                            alert('Erro ao marcar lançamento como pago');
                        }
                    }).fail(function () {
                        alert('Erro ao comunicar o servidor');
                    });
                });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.extra-content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('layouts.extra-content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/financeiro.blade.php ENDPATH**/ ?>