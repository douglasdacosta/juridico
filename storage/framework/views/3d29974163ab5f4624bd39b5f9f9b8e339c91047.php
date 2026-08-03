

<?php $__env->startSection('title', 'CRM'); ?>

<?php $__env->startSection('adminlte_css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/adminlte-custom.css')); ?>">
<?php $__env->stopSection(); ?>

<?php if(isset($tela) and $tela == 'pesquisa'): ?>
    <?php $__env->startSection('content_header'); ?>
        <?php if($perfil == 1): ?>
            <div class="form-group row">
                <h1 class="m-0 text-dark col-sm-11 col-form-label">Pesquisa de <?php echo e($nome_tela); ?></h1>
                <div class="col-sm-1">
                    <?php echo $__env->make('layouts.nav-open-incluir', ['rotaIncluir' => $rotaIncluir], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        <?php endif; ?>
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('content'); ?>
    <div class="right_col" role="main">

        <?php if($perfil == 1): ?>
            <form id="filtro" action="<?php echo e(route($rotaIndex)); ?>" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
                <div class="form-group row">
                    <label for="nome" class="col-sm-1 col-form-label">Nome</label>
                    <div class="col-sm-4">
                        <input type="text" id="nome" name="nome" class="form-control" value="<?php echo e(isset($request) ? $request->input('nome') : ''); ?>">
                    </div>

                    <label for="numero_processo" class="col-sm-1 col-form-label">Processo</label>
                    <div class="col-sm-3">
                        <input type="text" id="numero_processo" name="numero_processo" class="form-control mask_numero_processo" maxlength="25" value="<?php echo e(isset($request) ? $request->input('numero_processo') : ''); ?>">
                    </div>

                </div>

                <div class="form-group row">
                    <label for="status" class="col-sm-1 col-form-label"></label>
                    <select class="form-control col-md-1" id="status" name="status">
                        <option value="">Todos</option>
                        <option value="A" <?php if(isset($request) && $request->input('status') == 'A'): ?> selected <?php endif; ?>>Ativo</option>
                        <option value="I" <?php if(isset($request) && $request->input('status') == 'I'): ?> selected <?php endif; ?>>Inativo</option>
                    </select>

                    <label for="ativo" class="col-sm-1 col-form-label"></label>
                    <select class="form-control col-md-2" id="ativo" name="ativo">
                        <option value="1" <?php if(isset($request) && $request->input('ativo') === '1'): ?> selected <?php endif; ?>>Ativo lógico</option>
                        <option value="0" <?php if(isset($request) && $request->input('ativo') === '0'): ?> selected <?php endif; ?>>Desativado</option>
                    </select>
                </div>
                <div class="form-group row">
                    <div class="col-sm-5">
                        <button type="submit" class="btn btn-primary">Pesquisar</button>
                        <a href="<?php echo e(route($rotaExportarCsv, request()->query())); ?>" class="btn btn-secondary">CSV</a>
                        <a href="<?php echo e(route($rotaExportarPdf, request()->query())); ?>" class="btn btn-outline-secondary" target="_blank">PDF</a>
                    </div>
                    <div class="col-sm-5">
                    </div>
                </div>
            </form>
        <?php endif; ?>
        <div class="form-group">
          <label class="control-label col-md-3 col-sm-3 col-xs-12" for=""></label>
          <div class="col-md-12 col-sm-12 col-xs-12">
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
                      <th>Nome</th>
                      <th>CPF</th>
                      <th>Telefone</th>
                      <th>Email</th>
                                            <th>Responsáveis</th>
                                            <th>Ativo</th>
                                            <th>Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php if(isset($clientes)): ?>
                        <?php $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                            <th scope="row"><a href="<?php echo e(URL::route($rotaAlterar, ['id' => $cliente->id])); ?>"><?php echo e($cliente->id); ?></a></th>
                              <td><?php echo e($cliente->nome); ?></td>
                              <td class='mask_cpf'><?php echo e($cliente->cpf); ?></td>
                              <td class='mask_phone'><?php echo e($cliente->telefone); ?></td>
                              <td><?php echo e($cliente->email); ?></td>
                                                            <td><?php echo e($cliente->responsaveis->pluck('name')->implode(', ')); ?></td>
                                                            <td><?php echo e($cliente->ativo ? 'Sim' : 'Não'); ?></td>
                                                            <td>
                                                                <?php if($cliente->ativo): ?>
                                                                        <form action="<?php echo e(route('desativar-clientes')); ?>" method="post" style="display:inline;">
                                                                                <?php echo csrf_field(); ?>
                                                                                <input type="hidden" name="id" value="<?php echo e($cliente->id); ?>">
                                                                                <button type="submit" class="btn btn-sm btn-outline-danger">Desativar</button>
                                                                        </form>
                                                                <?php endif; ?>
                                                            </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
    </div>

    <?php $__env->stopSection(); ?>
<?php else: ?>
<?php $__env->startSection('content'); ?>
        <?php if($tela == 'alterar'): ?>
            <?php $__env->startSection('content_header'); ?>
                <h1 class="m-0 text-dark">Alteração de <?php echo e($nome_tela); ?></h1>
            <?php $__env->stopSection(); ?>
            <form id="alterar" action="<?php echo e(route($rotaAlterar)); ?>" data-parsley-validate="" class="form-horizontal form-label-left" method="post">
                <input type="hidden" id="id" name="id" class="form-control col-md-7 col-xs-12" readonly="true" value="<?php echo e($clientes[0]->id ?? ''); ?>">
        <?php else: ?>
            <?php $__env->startSection('content_header'); ?>
                <h1 class="m-0 text-dark">Inclusão de <?php echo e($nome_tela); ?></h1>
            <?php $__env->stopSection(); ?>
            <form id="incluir" action="<?php echo e(route($rotaIncluir)); ?>" data-parsley-validate="" class="form-horizontal form-label-left" method="post">
        <?php endif; ?>
            <?php echo csrf_field(); ?>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Tipo de Pessoa</label>
                <div class="col-sm-6 pt-2">
                    <?php $tipoPessoa = old('tipo_pessoa', $clientes[0]->tipo_pessoa ?? 'F'); ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tipo_pessoa" id="tipo_pf" value="F"
                            <?php echo e($tipoPessoa === 'F' ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="tipo_pf">Pessoa Física (CPF)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tipo_pessoa" id="tipo_pj" value="J"
                            <?php echo e($tipoPessoa === 'J' ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="tipo_pj">Pessoa Jurídica (CNPJ)</label>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="nome" class="col-sm-2 col-form-label">Nome</label>
                <div class="col-sm-4">
                <input type="text" class="form-control is-invalid" required id="nome" name="nome" value="<?php echo e(old('nome', $clientes[0]->nome ?? '')); ?>">
                </div>
            </div>

            
            <div class="form-group row" id="bloco_cpf">
                <label for="cpf" class="col-sm-2 col-form-label">CPF <small>(opcional)</small></label>
                <div class="col-sm-2">
                <input type="text" class="form-control mask_cpf <?php $__errorArgs = ['cpf'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="cpf" name="cpf" value="<?php echo e(old('cpf', $clientes[0]->cpf ?? '')); ?>">
                <?php $__errorArgs = ['cpf'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            
            <div id="bloco_pj">
                <div class="form-group row">
                    <label for="cnpj" class="col-sm-2 col-form-label">CNPJ <small>(opcional)</small></label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control mask_cnpj <?php $__errorArgs = ['cnpj'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="cnpj" name="cnpj" value="<?php echo e(old('cnpj', $clientes[0]->cnpj ?? '')); ?>">
                        <?php $__errorArgs = ['cnpj'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-12">
                        <h6 class="mb-2">Sócios / Representantes <small class="text-muted">(opcional)</small></h6>
                        <table class="table table-sm table-bordered" id="tabela_socios">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nome do Sócio</th>
                                    <th>CPF do Sócio</th>
                                    <th>Endereço do Sócio</th>
                                    <th style="width:60px"></th>
                                </tr>
                            </thead>
                            <tbody id="socios_tbody">
                                <?php
                                    $sociosExistentes = old('socios_nome')
                                        ? array_map(null,
                                            old('socios_nome', []),
                                            old('socios_cpf', []),
                                            old('socios_endereco', []))
                                        : ($clientes[0]->socios ?? []);
                                ?>
                                <?php $__empty_1 = true; $__currentLoopData = $sociosExistentes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $socio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><input type="text" class="form-control form-control-sm" name="socios_nome[]" value="<?php echo e(is_array($socio) ? ($socio['nome'] ?? '') : ''); ?>" maxlength="255"></td>
                                    <td><input type="text" class="form-control form-control-sm mask_cpf_socio" name="socios_cpf[]" value="<?php echo e(is_array($socio) ? ($socio['cpf'] ?? '') : ''); ?>" maxlength="14"></td>
                                    <td><input type="text" class="form-control form-control-sm" name="socios_endereco[]" value="<?php echo e(is_array($socio) ? ($socio['endereco'] ?? '') : ''); ?>" maxlength="500"></td>
                                    <td class="text-center"><button type="button" class="btn btn-sm btn-danger btn-remover-socio">&#10005;</button></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td><input type="text" class="form-control form-control-sm" name="socios_nome[]" maxlength="255"></td>
                                    <td><input type="text" class="form-control form-control-sm mask_cpf_socio" name="socios_cpf[]" maxlength="14"></td>
                                    <td><input type="text" class="form-control form-control-sm" name="socios_endereco[]" maxlength="500"></td>
                                    <td class="text-center"><button type="button" class="btn btn-sm btn-danger btn-remover-socio">&#10005;</button></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-secondary" id="btn_add_socio">
                            + Adicionar Sócio
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="endereco" class="col-sm-2 col-form-label">Endereço</label>
                <div class="col-sm-7">
                <input type="text" class="form-control" id="endereco" name="endereco" value="<?php echo e(old('endereco', $clientes[0]->endereco ?? '')); ?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="numero" class="col-sm-2 col-form-label">Numero</label>
                <div class="col-sm-1">
                <input type="text" class="form-control sonumeros" id="numero" name="numero" value="<?php echo e(old('numero', $clientes[0]->numero ?? '')); ?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="bairro" class="col-sm-2 col-form-label">Bairro</label>
                <div class="col-sm-4">
                <input type="text" class="form-control" id="bairro" name="bairro" value="<?php echo e(old('bairro', $clientes[0]->bairro ?? '')); ?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="cidade" class="col-sm-2 col-form-label">Cidade</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="cidade" name="cidade" value="<?php echo e(old('cidade', $clientes[0]->cidade ?? '')); ?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-2">
                <input type="text" class="form-control <?php $__errorArgs = ['estado'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="estado" name="estado" maxlength="2" value="<?php echo e(old('estado', $clientes[0]->estado ?? '')); ?>" oninput="this.value = this.value.toUpperCase().slice(0,2)">
                <?php $__errorArgs = ['estado'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
            <div class="form-group row">
                <label for="cep" class="col-sm-2 col-form-label">Cep</label>
                <div class="col-sm-2">
                <input type="text" class="form-control cep" id="cep" name="cep" value="<?php echo e(old('cep', $clientes[0]->cep ?? '')); ?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="telefone" class="col-sm-2 col-form-label">Telefone</label>
                <div class="col-sm-2">
                <input type="text" class="form-control mask_phone" id="telefone" name="telefone" value="<?php echo e(old('telefone', $clientes[0]->telefone ?? '')); ?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="email" class="col-sm-2 col-form-label">Email <small class="text-muted">(opcional)</small></label>
                <div class="col-sm-4">
                <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="email" name="email" value="<?php echo e(old('email', $clientes[0]->email ?? '')); ?>">
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="form-group row">
                <label for="responsaveis" class="col-sm-2 col-form-label">Responsáveis</label>
                <div class="col-sm-6">
                    <?php
                        $responsaveisSelecionados = old('responsaveis', isset($clientes[0]) ? $clientes[0]->responsaveis->pluck('id')->all() : []);
                    ?>
                    <select multiple class="form-control" id="responsaveis" name="responsaveis[]" size="5">
                        <?php $__currentLoopData = ($responsaveisOptions ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $nome): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($id); ?>" <?php echo e(in_array($id, $responsaveisSelecionados) ? 'selected' : ''); ?>><?php echo e($nome); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="status" class="col-sm-2 col-form-label"></label>
                <select class="form-control col-md-1" id="status" name="status">
                    <option value="A" <?php if(old('status', $clientes[0]->status ?? 'A') == 'A'): ?> selected <?php endif; ?>>Ativo</option>
                    <option value="I" <?php if(old('status', $clientes[0]->status ?? '') == 'I'): ?> selected <?php endif; ?>>Inativo</option>
                </select>
            </div>

            <div class="form-group row">
                <label for="lgpd_consent" class="col-sm-2 col-form-label">LGPD</label>
                <div class="col-sm-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="lgpd_consent" name="lgpd_consent"
                            <?php echo e(old('lgpd_consent', isset($clientes[0]->lgpd_consent_at) && !empty($clientes[0]->lgpd_consent_at) ? '1' : '') ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="lgpd_consent">
                            Consentimento para tratamento de dados pessoais
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="lgpd_purpose" class="col-sm-2 col-form-label">Finalidade LGPD</label>
                <div class="col-sm-6">
                    <textarea class="form-control <?php $__errorArgs = ['lgpd_purpose'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="lgpd_purpose" name="lgpd_purpose" rows="3" maxlength="1000"><?php echo e(old('lgpd_purpose', $clientes[0]->lgpd_purpose ?? '')); ?></textarea>
                    <?php $__errorArgs = ['lgpd_purpose'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-5">
                    <button class="btn btn-danger" onclick="window.history.back();" type="button">Cancelar</button>
                </div>
                <div class="col-sm-5">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </form>

    <?php $__env->stopSection(); ?>
<?php endif; ?>
<?php $__env->startSection('js'); ?>
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="js/jquery.mask.js"></script>
    <script src="js/select2.min.js"></script>
    <script src="js/main_custom.js"></script>
    <script src="js/acoes.js"></script>
    <script>
        $(document).ready(function() {
            $('.mask_numero_processo').mask('0000000-00.0000.0.00.0000');
            $('.mask_cpf').mask('000.000.000-00');
            $('.mask_cnpj').mask('00.000.000/0000-00');
            $('.mask_cpf_socio').mask('000.000.000-00');

            function atualizarTipoPessoa() {
                var tipo = $('input[name="tipo_pessoa"]:checked').val();
                if (tipo === 'J') {
                    $('#bloco_cpf').hide();
                    $('#bloco_pj').show();
                } else {
                    $('#bloco_cpf').show();
                    $('#bloco_pj').hide();
                }
            }

            $('input[name="tipo_pessoa"]').on('change', atualizarTipoPessoa);
            atualizarTipoPessoa();

            // Adicionar linha de sócio
            $('#btn_add_socio').on('click', function() {
                var row = '<tr>' +
                    '<td><input type="text" class="form-control form-control-sm" name="socios_nome[]" maxlength="255"></td>' +
                    '<td><input type="text" class="form-control form-control-sm mask_cpf_socio" name="socios_cpf[]" maxlength="14"></td>' +
                    '<td><input type="text" class="form-control form-control-sm" name="socios_endereco[]" maxlength="500"></td>' +
                    '<td class="text-center"><button type="button" class="btn btn-sm btn-danger btn-remover-socio">&#10005;</button></td>' +
                    '</tr>';
                $('#socios_tbody').append(row);
                $('#socios_tbody .mask_cpf_socio').last().mask('000.000.000-00');
            });

            // Remover linha de sócio
            $(document).on('click', '.btn-remover-socio', function() {
                var tbody = $('#socios_tbody');
                if (tbody.find('tr').length > 1) {
                    $(this).closest('tr').remove();
                } else {
                    $(this).closest('tr').find('input').val('');
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\juridico\resources\views\clientes.blade.php ENDPATH**/ ?>