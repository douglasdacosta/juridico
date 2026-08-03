

<?php $__env->startSection('title', env('APP_NAME')); ?>

<?php $__env->startSection('adminlte_css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/adminlte-custom.css')); ?>">
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

            <form id="filtro" action="filiais" method="get" class="form-horizontal form-label-left" novalidate>
                <div class="form-group row">
                    <label for="nome" class="col-sm-1 col-form-label text-right">Nome</label>
                    <div class="col-sm-4">
                        <input type="text" id="nome" name="nome" class="form-control" value="<?php echo e($request->input('nome') ?? ''); ?>">
                    </div>
                    <label for="ativo" class="col-sm-1 col-form-label text-right">Situação</label>
                    <div class="col-sm-2">
                        <select class="form-control" id="ativo" name="ativo">
                            <option value="">Todos</option>
                            <option value="1" <?php echo e($request->input('ativo') === '1' ? 'selected' : ''); ?>>Ativo</option>
                            <option value="0" <?php echo e($request->input('ativo') === '0' ? 'selected' : ''); ?>>Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-5">
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
                                <th>Nome</th>
                                <th>CNPJ</th>
                                <th>Endereço</th>
                                <th>Situação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $filiais ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <th scope="row"><a href="<?php echo e(route($rotaAlterar, ['id' => $filial->id])); ?>"><?php echo e($filial->id); ?></a></th>
                                    <td><?php echo e($filial->nome); ?></td>
                                    <td><?php echo e($filial->cnpj); ?></td>
                                    <td><?php echo e($filial->endereco); ?></td>
                                    <td><?php echo e($filial->ativo ? 'Ativo' : 'Inativo'); ?></td>
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
            <form id="<?php echo e($tela); ?>" action="<?php echo e($tela == 'alterar' ? route($rotaAlterar) : route($rotaIncluir)); ?>" method="post">
                <?php echo csrf_field(); ?>
                <?php if($tela == 'alterar'): ?>
                    <input type="hidden" name="id" value="<?php echo e($filial->id ?? ''); ?>">
                <?php endif; ?>

                <div class="container">
                    <div class="row row-cols-md-3 g-3 mt-2">
                        <div class="col-md-4">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['nome'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="nome" name="nome" maxlength="255" value="<?php echo e(old('nome', $filial->nome ?? '')); ?>">
                            <?php $__errorArgs = ['nome'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-md-3">
                            <label for="cnpj" class="form-label">CNPJ</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['cnpj'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="cnpj" name="cnpj" maxlength="20" value="<?php echo e(old('cnpj', $filial->cnpj ?? '')); ?>">
                            <?php $__errorArgs = ['cnpj'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-md-5">
                            <label for="endereco" class="form-label">Endereço</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['endereco'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="endereco" name="endereco" maxlength="255" value="<?php echo e(old('endereco', $filial->endereco ?? '')); ?>">
                            <?php $__errorArgs = ['endereco'];
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
                        <div class="col-md-2">
                            <label for="ativo" class="form-label">Ativo</label>
                            <select class="form-control" id="ativo" name="ativo">
                                <option value="1" <?php echo e(old('ativo', isset($filial) ? (int) $filial->ativo : 1) == 1 ? 'selected' : ''); ?>>Sim</option>
                                <option value="0" <?php echo e(old('ativo', isset($filial) ? (int) $filial->ativo : 1) == 0 ? 'selected' : ''); ?>>Não</option>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.extra-content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('layouts.extra-content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\juridico\resources\views/filiais.blade.php ENDPATH**/ ?>