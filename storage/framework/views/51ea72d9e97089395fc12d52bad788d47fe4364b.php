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
            <h1 class="m-0 text-dark col-sm-11 col-form-label">Modelos de Documento</h1>
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

            <form action="<?php echo e(route('modelos-documento')); ?>" method="get" class="form-horizontal form-label-left" novalidate>
                <div class="form-group row">
                    <label for="nome" class="col-sm-1 col-form-label text-right">Nome</label>
                    <div class="col-sm-3">
                        <input type="text" id="nome" name="nome" class="form-control" value="<?php echo e($request->input('nome') ?? ''); ?>">
                    </div>
                    <label for="tipo" class="col-sm-1 col-form-label text-right">Tipo</label>
                    <div class="col-sm-2">
                        <select class="form-control" id="tipo" name="tipo">
                            <option value="">Todos</option>
                            <?php $__currentLoopData = $tipoOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $valor => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($valor); ?>" <?php echo e($request->input('tipo') === $valor ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-sm-1">
                        <button type="submit" class="btn btn-primary">Pesquisar</button>
                    </div>
                </div>
            </form>

            <div class="x_panel">
                <div class="x_content">
                    <table class="table table-striped text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $modelos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modelo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <th scope="row"><a href="<?php echo e(route($rotaAlterar, ['id' => $modelo->id])); ?>"><?php echo e($modelo->id); ?></a></th>
                                    <td><?php echo e($modelo->nome); ?></td>
                                    <td><?php echo e($modelo->tipo_label); ?></td>
                                    <td>
                                        <span class="badge <?php echo e($modelo->ativo ? 'badge-success' : 'badge-secondary'); ?>">
                                            <?php echo e($modelo->ativo ? 'Ativo' : 'Inativo'); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route($rotaAlterar, ['id' => $modelo->id])); ?>" class="btn btn-link btn-sm">Editar</a>
                                        <?php if($modelo->ativo): ?>
                                            <form action="<?php echo e(route('desativar-modelos-documento')); ?>" method="post" style="display:inline;">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="id" value="<?php echo e($modelo->id); ?>">
                                                <button type="submit" class="btn btn-link btn-sm text-danger" onclick="return confirm('Desativar este modelo?')">Desativar</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5">Nenhum modelo de documento encontrado.</td>
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
        <h1 class="m-0 text-dark"><?php echo e($tela == 'alterar' ? 'Alteração de' : 'Inclusão de'); ?> Modelo de Documento</h1>
    <?php $__env->stopSection(); ?>

    <?php $__env->startSection('content'); ?>
    
        <div class="right_col" role="main">
            <form action="<?php echo e($tela == 'alterar' ? route($rotaAlterar) : route($rotaIncluir)); ?>" method="post">
                <?php echo csrf_field(); ?>
                <?php if($tela == 'alterar'): ?>
                    <input type="hidden" name="id" value="<?php echo e($modelo->id ?? ''); ?>">
                <?php endif; ?>

                <div class="container-fluid">
                    <div class="row row-cols-md-2 g-3 mt-2">
                        <div class="col-md-8">
                            <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php $__errorArgs = ['nome'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="nome" name="nome" value="<?php echo e(old('nome', $modelo->nome ?? '')); ?>">
                            <?php $__errorArgs = ['nome'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-4">
                            <label for="tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                            <select class="form-control <?php $__errorArgs = ['tipo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="tipo" name="tipo">
                                <?php $__currentLoopData = $tipoOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $valor => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($valor); ?>" <?php echo e((string) old('tipo', $modelo->tipo ?? '') === (string) $valor ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['tipo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label for="corpo" class="form-label">Conteúdo <span class="text-danger">*</span></label>
                            <textarea class="form-control <?php $__errorArgs = ['corpo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="corpo" name="corpo" rows="15"><?php echo e(old('corpo', $modelo->corpo ?? '')); ?></textarea>
                            <?php $__errorArgs = ['corpo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <small class="form-text text-muted">
                                Placeholders disponíveis (substituídos automaticamente ao gerar o documento):
                                <?php $__currentLoopData = $placeholders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $placeholder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <code><?php echo e($placeholder); ?></code>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </small>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3 form-check">
                            <?php $ativoChecked = old('ativo', isset($modelo) ? ($modelo->ativo ? '1' : '0') : '1'); ?>
                            <input class="form-check-input" type="checkbox" id="ativo" name="ativo" value="1" <?php echo e($ativoChecked == '1' ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="ativo">Ativo</label>
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
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        if (typeof tinymce !== 'undefined' && document.getElementById('corpo')) {
            tinymce.init({
                selector: '#corpo',
                height: 400,
                language: 'pt_BR',
                menubar: false,
                plugins: 'lists link table code',
                toolbar: 'undo redo | bold italic underline | bullist numlist | link table | code',
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.extra-content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('layouts.extra-content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/modelos-documento.blade.php ENDPATH**/ ?>