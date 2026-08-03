

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

            <form id="filtro" method="get" class="form-horizontal form-label-left" novalidate>
                <div class="form-group row">
                    <div class="col-sm-3">
                        <select class="form-control select2-ajax-clientes" name="cliente_id" style="width: 100%;">
                            <option value="">Cliente</option>
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
                    <div class="col-sm-3">
                        <input type="text" id="numero_processo" name="numero_processo" class="form-control mask_numero_processo" maxlength="25" placeholder="Processo" value="<?php echo e($request->input('numero_processo') ?? ''); ?>">
                    </div>
                    <div class="col-sm-2">
                        <select class="form-control" name="ativo">
                            <option value="">Status</option>
                            <option value="1" <?php echo e($request->input('ativo') === '1' ? 'selected' : ''); ?>>Ativo</option>
                            <option value="0" <?php echo e($request->input('ativo') === '0' ? 'selected' : ''); ?>>Inativo</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-primary" onclick="fazerPesquisa()">Pesquisar</button>
                    </div>
                </div>
            </form>

            <div class="x_panel">
                <div class="x_title">
                    <h4>Documentos</h4>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table class="table table-striped text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Arquivo</th>
                                <th>Versão</th>
                                <th>Cliente</th>
                                <th>Processo</th>
                                <th>Tipo de Ação</th>
                                <th>Andamento</th>
                                <th>Status</th>
                                    <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = ($documentos ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $documento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($documento->id); ?></td>
                                    <td><?php echo e($documento->nome_original); ?></td>
                                    <td>v<?php echo e($documento->versao); ?></td>
                                    <td><?php echo e($documento->cliente->nome ?? '-'); ?></td>
                                    <td><?php echo e($documento->processo->numero_processo ?? '-'); ?></td>
                                    <td>
                                        <?php if($documento->processo): ?>
                                            <span class="badge badge-primary"><?php echo e($documento->processo->tipoAcao?->nome ?? $documento->processo->tipo_acao); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
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
                                    <td>
                                        <?php echo e($documento->ativo ? 'Ativo' : 'Inativo'); ?>

                                        <?php if($documento->ativo): ?>
                                            <form action="<?php echo e(route('desativar-documentos')); ?>" method="post" style="display:inline;">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="id" value="<?php echo e($documento->id); ?>">
                                                <button type="submit" class="btn btn-link btn-sm">Desativar</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('preview-documentos', ['id' => $documento->id])); ?>" class="btn btn-link btn-sm" target="_blank">Preview</a>
                                        <a href="<?php echo e(route($rotaAlterar, ['id' => $documento->id])); ?>" class="btn btn-link btn-sm">Editar</a>
                                        <?php if($documento->ativo): ?>
                                            <form action="<?php echo e(route('excluir-documentos')); ?>" method="post" style="display:inline;">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="id" value="<?php echo e($documento->id); ?>">
                                                <button type="submit" class="btn btn-link btn-sm text-danger">Excluir</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
            <form action="<?php echo e($tela == 'alterar' ? route($rotaAlterar) : route($rotaIncluir)); ?>" method="post" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php if($tela == 'alterar'): ?>
                    <input type="hidden" name="id" value="<?php echo e($documento->id); ?>">
                <?php endif; ?>
                <?php if(!empty($processoRetornoId)): ?>
                    <input type="hidden" name="processo_retorno_id" value="<?php echo e($processoRetornoId); ?>">
                <?php endif; ?>
                <div class="container">
                    <div class="row row-cols-md-3 g-3 mt-2">
                        <div class="col-md-6">
                            <label for="arquivo" class="form-label"><?php echo e($tela == 'alterar' ? 'Substituir arquivo (opcional)' : 'Arquivo'); ?></label>
                            <input type="file" class="form-control <?php $__errorArgs = ['arquivo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="arquivo" name="arquivo">
                            <?php $__errorArgs = ['arquivo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <?php if($tela == 'alterar'): ?>
                                <small class="text-muted">Atual: <?php echo e($documento->nome_original); ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-3">
                            <label for="documento_base_id" class="form-label">Nova versão de</label>
                            <select class="form-control" id="documento_base_id" name="documento_base_id" <?php echo e($tela == 'alterar' ? 'disabled' : ''); ?>>
                                <option value="">Novo documento</option>
                                <?php $__currentLoopData = ($documentosBaseOptions ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $documentoBase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($documentoBase->id); ?>">#<?php echo e($documentoBase->id); ?> - <?php echo e($documentoBase->nome_original); ?> (v<?php echo e($documentoBase->versao); ?>)</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="row row-cols-md-3 g-3 mt-2">
                        <div class="col-md-4">
                            <label for="cliente_id" class="form-label">Cliente</label>
                            <select class="form-control select2-ajax-clientes" id="cliente_id" name="cliente_id" style="width: 100%;">
                                <option value="">Selecione</option>
                                <?php if(old('cliente_id', $documento->cliente_id ?? '')): ?>
                                    <?php
                                        $clienteSelecionado = \App\Models\Cliente::find(old('cliente_id', $documento->cliente_id ?? ''));
                                    ?>
                                    <?php if($clienteSelecionado): ?>
                                        <option value="<?php echo e($clienteSelecionado->id); ?>" selected>
                                            <?php echo e($clienteSelecionado->nome); ?><?php echo e($clienteSelecionado->cpf ? ' (CPF: ' . $clienteSelecionado->cpf . ')' : ''); ?>

                                        </option>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="processo_id" class="form-label">Processo</label>
                            <select class="form-control" id="processo_id" name="processo_id">
                                <option value="">Selecione</option>
                                <?php $__currentLoopData = ($processosOptions ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $numero): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($id); ?>" <?php echo e((string) old('processo_id', $documento->processo_id ?? ($processoSelecionado ?? '')) === (string) $id ? 'selected' : ''); ?>><?php echo e($numero); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="andamento_id" class="form-label">Andamento</label>
                            <select class="form-control" id="andamento_id" name="andamento_id">
                                <option value="">Selecione</option>
                                <?php $__currentLoopData = ($andamentosOptions ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $numero): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($id); ?>" <?php echo e((string) old('andamento_id', $documento->andamento_id ?? '') === (string) $id ? 'selected' : ''); ?>><?php echo e($numero); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="row row-cols-md-3 g-3 mt-4">
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
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="js/jquery.mask.js"></script>
    <script src="js/select2.min.js"></script>
    <script src="js/main_custom.js"></script>
    <script>
        // Configurar cabeçalho CSRF para todas requisições AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function fazerPesquisa() {
            const clienteId = $('#filtro').find('select[name="cliente_id"]').val();
            const numeroProcesso = $('#filtro').find('input[name="numero_processo"]').val();
            const ativo = $('#filtro').find('select[name="ativo"]').val();

            // Construir URL com parâmetros
            let url = '<?php echo e(route("documentos")); ?>?';
            const params = [];
            if (clienteId) params.push('cliente_id=' + encodeURIComponent(clienteId));
            if (numeroProcesso) params.push('numero_processo=' + encodeURIComponent(numeroProcesso));
            if (ativo !== '') params.push('ativo=' + encodeURIComponent(ativo));

            url += params.join('&');

            // Navegar mantendo a sessão
            window.location.href = url;
        }

        function inicializarSelect2() {
            // Inicializar Select2 AJAX para clientes
            $('.select2-ajax-clientes').select2({
                ajax: {
                    url: '<?php echo e(route("api.clientes.search")); ?>',
                    dataType: 'json',
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

        $(document).ready(function() {
            $('.mask_numero_processo').mask('0000000-00.0000.0.00.0000');
            inicializarSelect2();

            // Recarregar andamentos ao trocar o processo
            $('#processo_id').on('change', function() {
                const processoId = $(this).val();
                const $andamentoSelect = $('#andamento_id');
                $andamentoSelect.html('<option value="">Selecione</option>');

                if (!processoId) return;

                $.ajax({
                    url: '/api/andamentos/por-processo/' + processoId,
                    method: 'GET',
                    xhrFields: { withCredentials: true },
                    success: function(data) {
                        data.forEach(function(a) {
                            $andamentoSelect.append(
                                '<option value="' + a.id + '">#' + a.id + ' - ' + a.tipo + ' (' + a.data_andamento + ') - ' + a.descricao + '</option>'
                            );
                        });
                    }
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.extra-content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('layouts.extra-content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\juridico\resources\views/documentos.blade.php ENDPATH**/ ?>