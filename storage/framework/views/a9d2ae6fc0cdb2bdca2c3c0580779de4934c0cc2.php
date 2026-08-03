

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

            <form id="filtro" action="andamentos" method="get" class="form-horizontal form-label-left" novalidate>
                <div class="form-group row">
                    <label for="numero_processo" class="col-sm-2 col-form-label text-right">Processo</label>
                    <div class="col-sm-4">
                        <input type="text" id="numero_processo" name="numero_processo" class="form-control mask_numero_processo" maxlength="25" value="<?php echo e($request->input('numero_processo') ?? ''); ?>">
                    </div>
                    <label for="tipo" class="col-sm-1 col-form-label text-right">Tipo</label>
                    <div class="col-sm-3">
                        <select class="form-control" id="tipo" name="tipo">
                            <option value="">Todos</option>
                            <?php $__currentLoopData = ['peticao','audiencia','decisao','intimacao','recurso','outro']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($tipo); ?>" <?php echo e($request->input('tipo') === $tipo ? 'selected' : ''); ?>><?php echo e(ucfirst($tipo)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                    <h4>Timeline de Andamentos</h4>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table class="table table-striped text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Processo</th>
                                <th>Tipo</th>
                                <th>Data</th>
                                <th>Responsável</th>
                                <th>Criado por</th>
                                <th>Descrição</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $andamentos ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $andamento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <th scope="row"><a href="<?php echo e(route($rotaAlterar, ['id' => $andamento->id])); ?>"><?php echo e($andamento->id); ?></a></th>
                                    <td><?php echo e($andamento->processo->numero_processo ?? '-'); ?></td>
                                    <td><?php echo e(ucfirst($andamento->tipo)); ?></td>
                                    <td><?php echo e(optional($andamento->data_andamento)->format('d/m/Y')); ?></td>
                                    <td><?php echo e($andamento->usuario->name ?? '-'); ?></td>
                                    <td><?php echo e($andamento->criador->name ?? '-'); ?></td>
                                    <td style="max-width: 380px; white-space: normal;"><?php echo e($andamento->descricao); ?></td>
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
                    <input type="hidden" name="id" value="<?php echo e($andamento->id ?? ''); ?>">
                <?php endif; ?>

                <div class="container">
                    <div class="row row-cols-md-3 g-3 mt-2">
                        <div class="col-md-4">
                            <label for="processo_id" class="form-label">Processo</label>
                            <select class="form-control <?php $__errorArgs = ['processo_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="processo_id" name="processo_id">
                                <option value="">Selecione</option>
                                <?php $__currentLoopData = ($processosOptions ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $numero): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($id); ?>" <?php echo e((string) old('processo_id', $andamento->processo_id ?? '') === (string) $id ? 'selected' : ''); ?>><?php echo e($numero); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['processo_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-3">
                            <label for="tipo" class="form-label">Tipo</label>
                            <select class="form-control <?php $__errorArgs = ['tipo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="tipo" name="tipo">
                                <?php $__currentLoopData = ['peticao','audiencia','decisao','intimacao','recurso','outro']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($tipo); ?>" <?php echo e(old('tipo', $andamento->tipo ?? 'outro') === $tipo ? 'selected' : ''); ?>><?php echo e(ucfirst($tipo)); ?></option>
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
                        <div class="col-md-3">
                            <label for="data_andamento" class="form-label">Data</label>
                            <input type="date" class="form-control <?php $__errorArgs = ['data_andamento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="data_andamento" name="data_andamento" value="<?php echo e(old('data_andamento', isset($andamento->data_andamento) ? $andamento->data_andamento->format('Y-m-d') : '')); ?>">
                            <?php $__errorArgs = ['data_andamento'];
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
                        <div class="col-md-8">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control <?php $__errorArgs = ['descricao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="descricao" name="descricao" rows="5"><?php echo e(old('descricao', $andamento->descricao ?? '')); ?></textarea>
                            <?php $__errorArgs = ['descricao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <input type="hidden" name="usuario_id" value="<?php echo e(auth()->id()); ?>">
                    <input type="hidden" name="created_by" value="<?php echo e(auth()->id()); ?>">

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
        $(document).ready(function() {
            $('.mask_numero_processo').mask('0000000-00.0000.0.00.0000');
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.extra-content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('layouts.extra-content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\juridico\resources\views\andamentos.blade.php ENDPATH**/ ?>