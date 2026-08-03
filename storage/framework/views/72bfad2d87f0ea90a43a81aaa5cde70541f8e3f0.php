

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

            <form id="filtro" action="processos" method="get" class="form-horizontal form-label-left" novalidate>
                <div class="form-group row">
                    <label for="numero_processo" class="col-sm-2 col-form-label text-right">Número</label>
                    <div class="col-sm-3">
                        <input type="text" id="numero_processo" name="numero_processo" class="form-control mask_numero_processo" maxlength="25" value="<?php echo e($request->input('numero_processo') ?? ''); ?>">
                    </div>
                    <label for="status" class="col-sm-1 col-form-label text-right">Status</label>
                    <div class="col-sm-2">
                        <select class="form-control" id="status" name="status">
                            <option value="">Todos</option>
                            <option value="ativo" <?php echo e($request->input('status') === 'ativo' ? 'selected' : ''); ?>>Ativo</option>
                            <option value="encerrado" <?php echo e($request->input('status') === 'encerrado' ? 'selected' : ''); ?>>Encerrado</option>
                            <option value="suspenso" <?php echo e($request->input('status') === 'suspenso' ? 'selected' : ''); ?>>Suspenso</option>
                            <option value="arquivado" <?php echo e($request->input('status') === 'arquivado' ? 'selected' : ''); ?>>Arquivado</option>
                        </select>
                    </div>
                    <label for="cliente_id" class="col-sm-1 col-form-label text-right">Cliente</label>
                    <div class="col-sm-3">
                        <select class="form-control select2-ajax-clientes" id="cliente_id" name="cliente_id" style="width: 100%;">
                            <option value="">Todos</option>
                            <?php if($request->filled('cliente_id')): ?>
                                <?php
                                    $clienteSelecionado = \App\Models\Cliente::find($request->input('cliente_id'));
                                ?>
                                <?php if($clienteSelecionado): ?>
                                    <option value="<?php echo e($clienteSelecionado->id); ?>" selected>
                                        <?php echo e($clienteSelecionado->nome); ?><?php echo e($clienteSelecionado->cpf ? ' (CPF: ' . $clienteSelecionado->cpf . ')' : ''); ?>

                                    </option>
                                <?php endif; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="data_de" class="col-sm-2 col-form-label text-right">Data De</label>
                    <div class="col-sm-2">
                        <input type="text" id="data_de" name="data_de" class="form-control mask_date" placeholder="DD/MM/YYYY" value="<?php echo e($request->input('data_de') ?? ''); ?>">
                    </div>
                    <label for="data_ate" class="col-sm-2 col-form-label text-right">Data Até</label>
                    <div class="col-sm-2">
                        <input type="text" id="data_ate" name="data_ate" class="form-control mask_date" placeholder="DD/MM/YYYY" value="<?php echo e($request->input('data_ate') ?? ''); ?>">
                    </div>
                    <div class="col-sm-2">
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
                                <th>Número</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Responsável</th>
                                <th>Clientes</th>
                                <th>Filiais</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $processos ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $processo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <th scope="row"><a href="<?php echo e(route($rotaAlterar, ['id' => $processo->id])); ?>"><?php echo e($processo->id); ?></a></th>
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

                    
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Total: <?php echo e(count($processos ?? [])); ?> processo(s)</small>
                        </div>
                        <div>
                            <a href="<?php echo e(route('exportar-processos-csv', request()->query())); ?>" class="btn btn-secondary">
                                <i class="fas fa-file-csv"></i> Exportar CSV
                            </a>
                            <a href="<?php echo e(route('exportar-processos-pdf', request()->query())); ?>" class="btn btn-outline-secondary" target="_blank">
                                <i class="fas fa-file-pdf"></i> Exportar PDF
                            </a>
                        </div>
                    </div>
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

                <div class="container-fluid">
                    <?php if($tela == 'alterar' && isset($processo)): ?>
                        <div class="row">
                            <!-- Coluna Esquerda: Andamentos -->
                            <div class="col-md-5">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Timeline de Andamentos</h5>
                                        <button type="button"
                                                class="btn btn-sm btn-primary"
                                                data-toggle="modal"
                                                data-target="#modalNovoAndamento">
                                            <i class="fas fa-plus"></i> Novo Andamento
                                        </button>
                                    </div>
                                    <div class="card-body" style="max-height: 800px; overflow-y: auto;">

                                        <!-- Lista de andamentos existentes -->
                                        <div id="lista-andamentos">
                                            <?php $__empty_1 = true; $__currentLoopData = $processo->andamentos->sortByDesc('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $andamento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <div class="mb-3 p-3 border rounded andamento-item" data-id="<?php echo e($andamento->id); ?>">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <span class="badge bg-secondary"><?php echo e(ucfirst($andamento->tipo)); ?></span>
                                                            <small class="text-muted ms-2"><?php echo e($andamento->data_andamento->format('d/m/Y')); ?></small>
                                                        </div>
                                                        <div>
                                                            <button type="button" class="btn btn-sm btn-link p-0 me-2" onclick="editarAndamento(<?php echo e($andamento->id); ?>)">Editar</button>
                                                            <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="excluirAndamento(<?php echo e($andamento->id); ?>)">Excluir</button>
                                                        </div>
                                                    </div>
                                                    <p class="mb-1 mt-2"><?php echo e($andamento->descricao); ?></p>
                                                    <small class="text-muted">
                                                        Por: <?php echo e($andamento->usuario->name ?? 'N/A'); ?>

                                                        <?php if($andamento->criador): ?>
                                                            | Criado por: <?php echo e($andamento->criador->name); ?>

                                                        <?php endif; ?>
                                                    </small>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <p class="text-muted text-center">Nenhum andamento registrado.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- Fim col Andamentos -->

                            <!-- Coluna Direita: Dados Processuais -->
                            <div class="col-md-7">
                    <?php endif; ?>

                    <form id="<?php echo e($tela); ?>" action="<?php echo e($tela == 'alterar' ? route($rotaAlterar) : route($rotaIncluir)); ?>" method="post">

                        <?php echo csrf_field(); ?>
                        <?php if($tela == 'alterar'): ?>
                            <input type="hidden" name="id" value="<?php echo e($processo->id ?? ''); ?>">
                        <?php endif; ?>
                        <div class="row row-cols-md-3 g-3 mt-2">
                            <div class="col-md-4">
                                <label for="numero_processo" class="form-label">Número do processo</label>
                                <input type="text" class="form-control mask_numero_processo <?php $__errorArgs = ['numero_processo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="numero_processo" name="numero_processo" maxlength="25" value="<?php echo e(old('numero_processo', $processo->numero_processo ?? '')); ?>">
                                <?php $__errorArgs = ['numero_processo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-4">
                                <label for="vara_tribunal" class="form-label">Vara / Tribunal</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['vara_tribunal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="vara_tribunal" name="vara_tribunal" value="<?php echo e(old('vara_tribunal', $processo->vara_tribunal ?? '')); ?>">
                                <?php $__errorArgs = ['vara_tribunal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-4">
                                <label for="tipo_acao" class="form-label">Tipo de ação</label>
                                <select class="form-control select2-ajax-tipos-acao <?php $__errorArgs = ['tipo_acao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="tipo_acao" name="tipo_acao" style="width: 100%;">
                                    <option value="">Selecione</option>
                                    <?php if(old('tipo_acao', $processo->tipo_acao ?? '')): ?>
                                        <?php
                                            $tipoAcaoId = old('tipo_acao', $processo->tipo_acao ?? '');
                                            $tipoAcao = \App\Models\TipoAcao::find($tipoAcaoId);
                                        ?>
                                        <option value="<?php echo e($tipoAcaoId); ?>" selected><?php echo e($tipoAcao?->nome ?? $tipoAcaoId); ?></option>
                                    <?php endif; ?>
                                </select>
                                <?php $__errorArgs = ['tipo_acao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="row row-cols-md-3 g-3 mt-2">
                            <div class="col-md-3">
                                <label for="data_abertura" class="form-label">Data de abertura</label>
                                <input type="date" class="form-control <?php $__errorArgs = ['data_abertura'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="data_abertura" name="data_abertura" value="<?php echo e(old('data_abertura', isset($processo->data_abertura) ? $processo->data_abertura->format('Y-m-d') : '')); ?>">
                                <?php $__errorArgs = ['data_abertura'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="status" name="status">
                                    <?php $__currentLoopData = ['ativo' => 'Ativo', 'encerrado' => 'Encerrado', 'suspenso' => 'Suspenso', 'arquivado' => 'Arquivado']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $valor => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($valor); ?>" <?php echo e(old('status', $processo->status ?? 'ativo') === $valor ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-4">
                                <label for="responsavel_id" class="form-label">Responsável</label>
                                <select class="form-control <?php $__errorArgs = ['responsavel_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="responsavel_id" name="responsavel_id">
                                    <option value="">Selecione</option>
                                    <?php $__currentLoopData = ($responsaveisOptions ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $nome): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($id); ?>" <?php echo e((string) old('responsavel_id', $processo->responsavel_id ?? '') === (string) $id ? 'selected' : ''); ?>><?php echo e($nome); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['responsavel_id'];
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
                            <div class="col-md-6">
                                <label for="clientes" class="form-label">Clientes vinculados</label>
                                <select multiple class="form-control select2-ajax-clientes-multiple <?php $__errorArgs = ['clientes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="clientes" name="clientes[]" style="width: 100%;">
                                    <?php if(old('clientes')): ?>
                                        <?php
                                            $idsClientesSelecionados = old('clientes');
                                            $clientesOld = \App\Models\Cliente::whereIn('id', $idsClientesSelecionados)->get();
                                        ?>
                                        <?php $__currentLoopData = $clientesOld; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($cliente->id); ?>" selected>
                                                <?php echo e($cliente->nome); ?><?php echo e($cliente->cpf ? ' (CPF: ' . $cliente->cpf . ')' : ''); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php elseif(isset($processo) && $processo->clientes): ?>
                                        <?php $__currentLoopData = $processo->clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($cliente->id); ?>" selected>
                                                <?php echo e($cliente->nome); ?><?php echo e($cliente->cpf ? ' (CPF: ' . $cliente->cpf . ')' : ''); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                                <?php $__errorArgs = ['clientes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6">
                                <label for="filiais" class="form-label">Filiais</label>
                                <select multiple class="form-control select2-ajax-filiais-multiple <?php $__errorArgs = ['filiais'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="filiais" name="filiais[]" style="width: 100%;">
                                    <?php if(old('filiais')): ?>
                                        <?php
                                            $idsFiliaisSelecionadas = old('filiais');
                                            $filiaisOld = \App\Models\Filial::whereIn('id', $idsFiliaisSelecionadas)->get();
                                        ?>
                                        <?php $__currentLoopData = $filiaisOld; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($filial->id); ?>" selected>
                                                <?php echo e($filial->nome); ?><?php echo e($filial->cnpj ? ' (CNPJ: ' . $filial->cnpj . ')' : ''); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php elseif(isset($processo) && $processo->filiais): ?>
                                        <?php $__currentLoopData = $processo->filiais; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($filial->id); ?>" selected>
                                                <?php echo e($filial->nome); ?><?php echo e($filial->cnpj ? ' (CNPJ: ' . $filial->cnpj . ')' : ''); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                                <?php $__errorArgs = ['filiais'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
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
unset($__errorArgs, $__bag); ?>" id="observacoes" name="observacoes" rows="4"><?php echo e(old('observacoes', $processo->observacoes ?? '')); ?></textarea>
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
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary">Salvar</button>
                            </div>
                        </div>

                    <?php if($tela == 'incluir'): ?>
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i> <strong>Dica:</strong> Após salvar o processo, você poderá adicionar andamentos e documentos vinculados.
                        </div>
                    <?php endif; ?>
                    </form>

                    <?php if($tela == 'alterar' && isset($processo)): ?>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5>Documentos vinculados ao processo</h5>

                                <!-- Formulário de Upload Inline -->
                                <div class="mb-3">
                                    <button type="button" class="btn btn-sm btn-primary" onclick="toggleFormDocumento()">
                                        <i class="fas fa-plus"></i> Incluir Documento
                                    </button>
                                </div>

                                <div id="form-upload-documento" class="card mb-3" style="display: none;">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">Upload de Documento</h6>
                                        <div id="form-documento-upload">
                                            <input type="hidden" id="processo_id_doc" value="<?php echo e($processo->id); ?>">
                                            <input type="hidden" id="csrf_token_doc" value="<?php echo e(csrf_token()); ?>">

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="arquivo_upload" class="form-label">Arquivo *</label>
                                                        <input type="file" class="form-control" id="arquivo_upload" name="arquivo" required>
                                                        <small class="text-muted">Formatos: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF (máx 50MB)</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="documento_base_id" class="form-label">Nova versão de</label>
                                                        <select class="form-control" id="documento_base_id" name="documento_base_id">
                                                            <option value="">Novo documento</option>
                                                            <?php $__currentLoopData = ($processo->documentos ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option value="<?php echo e($doc->id); ?>">#<?php echo e($doc->id); ?> - <?php echo e($doc->nome_original); ?> (v<?php echo e($doc->versao); ?>)</option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="andamento_id_doc" class="form-label">Vincular a Andamento</label>
                                                        <select class="form-control" id="andamento_id_doc" name="andamento_id">
                                                            <option value="">Nenhum</option>
                                                            <?php $__currentLoopData = ($processo->andamentos ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $andamento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option value="<?php echo e($andamento->id); ?>">#<?php echo e($andamento->id); ?> - <?php echo e(ucfirst($andamento->tipo)); ?> (<?php echo e($andamento->data_andamento->format('d/m/Y')); ?>)</option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <button type="button" class="btn btn-success" onclick="uploadDocumento(event)">
                                                            <i class="fas fa-upload"></i> Upload
                                                        </button>
                                                        <button type="button" class="btn btn-secondary" onclick="toggleFormDocumento()">
                                                            <i class="fas fa-times"></i> Cancelar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <table class="table table-striped text-center" id="tabela-documentos">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Arquivo</th>
                                            <th>Versão</th>
                                            <th>Tipo de Ação</th>
                                            <th>Andamento</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = ($processo->documentos ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $documento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                <td><?php echo e($documento->id); ?></td>
                                                <td><?php echo e($documento->nome_original); ?></td>
                                                <td>v<?php echo e($documento->versao); ?></td>
                                                <td>
                                                    <span class="badge badge-primary"><?php echo e($processo->tipoAcao?->nome ?? $processo->tipo_acao); ?></span>
                                                </td>
                                                <td>
                                                    <?php if($documento->andamento): ?>
                                                        <span class="badge badge-info" title="<?php echo e($documento->andamento->descricao); ?>">
                                                            #<?php echo e(ucfirst($documento->andamento->tipo)); ?>

                                                            <?php echo e($documento->andamento->data_andamento->format('d/m/Y')); ?>

                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo e($documento->ativo ? 'Ativo' : 'Inativo'); ?></td>
                                                <td>
                                                    <a href="<?php echo e(route('preview-documentos', ['id' => $documento->id])); ?>" class="btn btn-link btn-sm" target="_blank">Preview</a>
                                                    <a href="<?php echo e(route('alterar-documentos', ['id' => $documento->id, 'processo_retorno_id' => $processo->id])); ?>" class="btn btn-link btn-sm">Editar</a>
                                                    <?php if($documento->ativo): ?>
                                                        <form action="<?php echo e(route('excluir-documentos')); ?>" method="post" style="display:inline;">
                                                            <input type="hidden" name="id" value="<?php echo e($documento->id); ?>">
                                                            <input type="hidden" name="processo_retorno_id" value="<?php echo e($processo->id); ?>">
                                                            <button type="submit" class="btn btn-link btn-sm text-danger">Excluir</button>
                                                        </form>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="6">Nenhum documento vinculado a este processo.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                            </div><!-- Fim col-md-7 Dados Processuais -->
                        </div><!-- Fim row duas colunas -->
                    <?php endif; ?>


                </div><!-- Fim container-fluid -->

        </div>

        <!-- Modal para Edição de Andamento -->
        <div class="modal fade" id="modalEditarAndamento" tabindex="-1" aria-labelledby="modalEditarAndamentoLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditarAndamentoLabel">Editar Andamento</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formEditarAndamento">
                            <input type="hidden" id="edit_andamento_id">
                            <input type="hidden" id="edit_processo_id">
                            <div class="mb-3">
                                <label for="edit_tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_tipo" name="tipo" placeholder="Ex: Petição, Audiência, Decisão..." required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_data_andamento" class="form-label">Data <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="edit_data_andamento" name="data_andamento" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_descricao" class="form-label">Descrição <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="edit_descricao" name="descricao" rows="4" required></textarea>
                            </div>
                            <div class="alert alert-info" role="alert">
                                <small><i class="fas fa-info-circle"></i> Todos os campos são obrigatórios</small>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="salvarEdicaoAndamento()">
                            <i class="fas fa-save"></i> Salvar Alterações
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Novo Andamento -->
        <div class="modal fade" id="modalNovoAndamento" tabindex="-1" aria-labelledby="modalNovoAndamentoLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalNovoAndamentoLabel">Novo Andamento</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="modal_novo_tipo" class="form-label">Tipo</label>
                            <input type="text" class="form-control" id="modal_novo_tipo" name="novo_tipo" placeholder="Ex: Petição, Audiência, Decisão..." required>
                        </div>
                        <div class="mb-3">
                            <label for="modal_nova_data_andamento" class="form-label">Data</label>
                            <input type="date" class="form-control" id="modal_nova_data_andamento"
                                   name="nova_data_andamento" value="<?php echo e(date('Y-m-d')); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="modal_nova_descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="modal_nova_descricao"
                                      name="nova_descricao" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="salvarAndamentoModal()">
                            <i class="fas fa-save"></i> Salvar Andamento
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php $__env->stopSection(); ?>
<?php endif; ?>

<?php $__env->startSection('js'); ?>
    <script src="js/jquery.mask.js"></script>
    <script src="js/select2.min.js"></script>
    <script src="js/main_custom.js"></script>
    <script>
        // Função auxiliar para atualizar o token CSRF da mesma tag dinamicamente
        function updateCsrfToken(response) {
            if (response && response._token) {
                $('meta[name="csrf-token"]').attr('content', response._token);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': response._token
                    }
                });
            }
        }

        // Função para recarregar a lista de andamentos
        function carregarAndamentos() {
            const processoId = <?php echo e(isset($processo) ? $processo->id : 'null'); ?>;
            if (!processoId) return;

            $.ajax({
                url: '<?php echo e(route("alterar-processos")); ?>?id=' + processoId,
                method: 'GET',
                xhrFields: {
                    withCredentials: true
                },
                success: function(html) {
                    // Extrair apenas a div de andamentos atualizada
                    const novaDiv = $(html).find('#lista-andamentos');
                    if (novaDiv.length) {
                        $('#lista-andamentos').replaceWith(novaDiv);
                    }
                },
                error: function(xhr) {
                    console.error('Erro ao carregar andamentos:', xhr);
                }
            });
        }

        // Configurar cabeçalho CSRF para todas requisições AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(function () {
            $('.mask_numero_processo').mask('0000000-00.0000.0.00.0000');

            // Inicializar Select2 AJAX para clientes (single)
            if ($('.select2-ajax-clientes').length) {
                $('.select2-ajax-clientes').select2({
                    ajax: {
                        url: '/api/clientes/search',
                        dataType: 'json',
                        _token: '<?php echo e(csrf_token()); ?>',
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

            // Inicializar Select2 AJAX para clientes (multiple)
            if ($('.select2-ajax-clientes-multiple').length) {
                $('.select2-ajax-clientes-multiple').select2({
                    ajax: {
                        url: '/api/clientes/search',
                        dataType: 'json',
                        _token: '<?php echo e(csrf_token()); ?>',
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
                    placeholder: 'Digite para buscar clientes',
                    allowClear: true,
                    multiple: true,
                    language: {
                        inputTooShort: function() { return 'Digite para buscar'; },
                        noResults: function() { return 'Nenhum cliente encontrado'; },
                        searching: function() { return 'Buscando...'; },
                        loadingMore: function() { return 'Carregando mais resultados...'; }
                    }
                });
            }

            // Inicializar Select2 AJAX para filiais (multiple)
            if ($('.select2-ajax-filiais-multiple').length) {
                $('.select2-ajax-filiais-multiple').select2({
                    ajax: {
                        url: '/api/filiais/search',
                        _token: '<?php echo e(csrf_token()); ?>',
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
                    placeholder: 'Digite para buscar filiais',
                    allowClear: true,
                    multiple: true,
                    language: {
                        inputTooShort: function() { return 'Digite para buscar'; },
                        noResults: function() { return 'Nenhuma filial encontrada'; },
                        searching: function() { return 'Buscando...'; },
                        loadingMore: function() { return 'Carregando mais resultados...'; }
                    }
                });
            }

            // Inicializar Select2 AJAX para tipos de ação
            if ($('.select2-ajax-tipos-acao').length) {
                $('.select2-ajax-tipos-acao').select2({
                    ajax: {
                        url: '<?php echo e(route("api.tipos-acao.search")); ?>',
                        dataType: 'json',
                        _token: '<?php echo e(csrf_token()); ?>',
                        xhrFields: {
                            withCredentials: true
                        },
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
                    placeholder: 'Digite para buscar tipo de ação',
                    allowClear: true,
                    language: {
                        inputTooShort: function() { return 'Digite para buscar'; },
                        noResults: function() { return 'Nenhum tipo encontrado'; },
                        searching: function() { return 'Buscando...'; },
                        loadingMore: function() { return 'Carregando mais resultados...'; }
                    }
                });
            }
        });

        function toggleFormDocumento() {
            $('#form-upload-documento').slideToggle();
        }

        function uploadDocumento(event) {
            event.preventDefault();

            // Validar se arquivo foi selecionado
            const arquivoInput = document.getElementById('arquivo_upload');
            if (!arquivoInput || !arquivoInput.files || arquivoInput.files.length === 0) {
                alert('Por favor, selecione um arquivo.');
                return;
            }

            // Criar FormData manualmente
            const formData = new FormData();
            formData.append('_token', document.getElementById('csrf_token_doc').value);
            formData.append('arquivo', arquivoInput.files[0]);
            formData.append('processo_id', document.getElementById('processo_id_doc').value);
            formData.append('processo_retorno_id', document.getElementById('processo_id_doc').value);

            const documentoBaseId = document.getElementById('documento_base_id').value;
            if (documentoBaseId) {
                formData.append('documento_base_id', documentoBaseId);
            }

            const andamentoId = document.getElementById('andamento_id_doc').value;
            if (andamentoId) {
                formData.append('andamento_id', andamentoId);
            }

            // Debug
            console.log('FormData criado com os seguintes campos:');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + (pair[1] instanceof File ? pair[1].name + ' (' + pair[1].size + ' bytes)' : pair[1]));
            }

            const uploadButton = event.target;
            const originalText = uploadButton.innerHTML;
            uploadButton.disabled = true;
            uploadButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

            $.ajax({
                url: '<?php echo e(route("incluir-documentos")); ?>',
                method: 'POST',
                xhrFields: {
                    withCredentials: true
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('Upload bem-sucedido:', response);
                    updateCsrfToken(response);
                    alert('Documento enviado com sucesso!');
                    // Recarregar página para mostrar novo documento na tabela
                    location.reload();
                },
                error: function(xhr) {
                    console.error('Erro no upload:', xhr);
                    uploadButton.disabled = false;
                    uploadButton.innerHTML = originalText;

                    let errorMsg = 'Erro ao enviar documento.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMsg += '\n';
                        Object.values(xhr.responseJSON.errors).forEach(errors => {
                            errors.forEach(error => {
                                errorMsg += '\n- ' + error;
                            });
                        });
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg += '\n' + xhr.responseJSON.message;
                    }
                    alert(errorMsg);
                }
            });
        }

        function salvarAndamentoModal() {
            const processoId = <?php echo e(isset($processo) ? $processo->id : 'null'); ?>;
            const tipo = $('#modal_novo_tipo').val();
            const dataAndamento = $('#modal_nova_data_andamento').val();
            const descricao = $('#modal_nova_descricao').val();

            if (!tipo || !dataAndamento || !descricao) {
                alert('Preencha todos os campos do andamento.');
                return;
            }

            $.ajax({
                url: '<?php echo e(route("incluir-andamentos")); ?>',
                method: 'POST',
                xhrFields: {
                    withCredentials: true
                },
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    processo_id: processoId,
                    tipo: tipo,
                    data_andamento: dataAndamento,
                    descricao: descricao,
                    usuario_id: <?php echo e(auth()->id()); ?>,
                    created_by: <?php echo e(auth()->id()); ?>

                },
                success: function(response) {
                    updateCsrfToken(response);
                    $('#modalNovoAndamento').modal('hide');
                    carregarAndamentos();
                    alert('Andamento criado com sucesso!');
                    // Sem reload para preservar sessão
                },
                error: function(xhr) {
                    alert('Erro ao salvar andamento: ' + (xhr.responseJSON?.message || 'Erro desconhecido'));
                }
            });
        }

        // Limpar formulário ao fechar modal
        $('#modalNovoAndamento').on('hidden.modal', function () {
            $('#modal_novo_tipo').val('outro');
            $('#modal_nova_data_andamento').val('<?php echo e(date("Y-m-d")); ?>');
            $('#modal_nova_descricao').val('');
        });

        // Limpar formulário de edição ao fechar modal
        $('#modalEditarAndamento').on('hidden.modal', function () {
            $('#edit_andamento_id').val('');
            $('#edit_processo_id').val('');
            $('#edit_tipo').val('outro');
            $('#edit_data_andamento').val('');
            $('#edit_descricao').val('');
        });

        function editarAndamento(id) {
            event.preventDefault();
            // Buscar dados do andamento via AJAX
            $.ajax({
                url: '/api/andamentos/' + id,
                method: 'GET',
                xhrFields: {
                    withCredentials: true
                },
                success: function(andamento) {
                    // Atualizar token ANTES de abrir o modal
                    updateCsrfToken(andamento);

                    // Preencher o formulário do modal
                    $('#edit_andamento_id').val(andamento.id);
                    $('#edit_processo_id').val(andamento.processo_id);
                    $('#edit_tipo').val(andamento.tipo);
                    $('#edit_data_andamento').val(andamento.data_andamento);
                    $('#edit_descricao').val(andamento.descricao);

                    // Mostrar o modal
                    $('#modalEditarAndamento').modal('show');
                },
                error: function(xhr) {
                    alert('Erro ao carregar andamento: ' + (xhr.responseJSON?.message || 'Erro desconhecido'));
                }
            });
        }

        function salvarEdicaoAndamento() {
            const id = $('#edit_andamento_id').val();
            const tipo = $('#edit_tipo').val();
            const dataAndamento = $('#edit_data_andamento').val();
            const descricao = $('#edit_descricao').val();

            // Validação
            if (!tipo || !dataAndamento || !descricao.trim()) {
                alert('Por favor, preencha todos os campos obrigatórios.');
                return;
            }

            // Desabilitar botão de salvar
            const btnSalvar = event.target;
            const textoOriginal = btnSalvar.innerHTML;
            btnSalvar.disabled = true;
            btnSalvar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';

            $.ajax({
                url: '<?php echo e(route("alterar-andamentos")); ?>',
                method: 'POST',
                xhrFields: {
                    withCredentials: true
                },
                data: {
                    id: id,
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    processo_id: $('#edit_processo_id').val() || <?php echo e(isset($processo) ? $processo->id : 'null'); ?>,
                    tipo: tipo,
                    data_andamento: dataAndamento,
                    descricao: descricao,
                    usuario_id: <?php echo e(auth()->id()); ?>

                },
                success: function(response) {
                    updateCsrfToken(response);
                    btnSalvar.disabled = false;
                    btnSalvar.innerHTML = textoOriginal;
                    $('#modalEditarAndamento').modal('hide');
                    carregarAndamentos();
                    alert('Andamento atualizado com sucesso!');
                    // Sem reload para preservar sessão
                },
                error: function(xhr) {
                    alert('Erro ao incluir andamento: ' + (xhr.responseJSON?.message || 'Erro desconhecido'));
                    btnSalvar.disabled = false;
                    btnSalvar.innerHTML = textoOriginal;
                }
            });
        }

        function excluirAndamento(id) {
            if (!confirm('Deseja realmente excluir este andamento?')) {
                return;
            }

            $.ajax({
                url: '<?php echo e(route("alterar-andamentos")); ?>?id=' + id,
                method: 'DELETE',
                xhrFields: {
                    withCredentials: true
                },
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    updateCsrfToken(response);

                    // Remover o elemento da lista com animação
                    $('[data-id="' + id + '"]').fadeOut(300, function() {
                        $(this).remove();

                        // Se não houver mais andamentos, mostrar mensagem
                        if ($('#lista-andamentos').children('.andamento-item').length === 0) {
                            $('#lista-andamentos').html('<p class="text-muted text-center">Nenhum andamento registrado.</p>');
                        }
                    });

                    alert('Andamento excluído com sucesso!');
                },
                error: function(xhr) {
                    alert('Erro ao excluir andamento: ' + (xhr.responseJSON?.message || 'Erro desconhecido'));
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.extra-content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('layouts.extra-content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\juridico\resources\views\processos.blade.php ENDPATH**/ ?>