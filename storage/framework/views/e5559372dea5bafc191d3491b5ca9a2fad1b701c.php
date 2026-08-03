

<?php $__env->startSection('title', 'CRM'); ?>

<?php $__env->startSection('adminlte_css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/adminlte-custom.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content_top_nav_left'); ?>
    <?php echo $__env->make('layouts.navbar_left', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php if(isset($tela) and $tela == 'pesquisa'): ?>
    <?php $__env->startSection('content_header'); ?>
    <div class="form-group row">
        <h1 class="m-0 text-dark col-sm-11 col-form-label">Pesquisa de <?php echo e($nome_tela); ?></h1>
        <div class="col-sm-1">
            <?php echo $__env->make('layouts.nav-open-incluir', ['rotaIncluir => $rotaIncluir'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('content'); ?>
    
    <div class="right_col" role="main">

        <form id="filtro" action="perfis" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
            <div class="form-group row">
                <label for="id" class="col-sm-1 col-form-label text-right">Código</label>
                <div class="col-sm-2">
                    <input type="text" id="id" name="id" class="form-control" value="<?php if(isset($request) && $request->input('id') != ''): ?><?php echo e($request->input('id')); ?><?php else: ?> <?php endif; ?>">
                </div>
                <label for="nome" class="col-sm-1 col-form-label text-right">Nome</label>
                <div class="col-sm-3">
                    <input type="text" id="nome" name="nome" class="form-control" value="<?php if(isset($request) && trim($request->input('nome')) != ''): ?><?php echo e($request->input('nome')); ?><?php else: ?> <?php endif; ?>">
                </div>
                <label for="ativo" class="col-sm-1 col-form-label text-right">Situação</label>
                <select class="form-control col-md-1" id="ativo" name="ativo">
                    <option value="A" <?php if(isset($request) && $request->input('ativo') == 'A'): ?><?php echo e(' selected '); ?><?php else: ?> <?php endif; ?>>Ativo</option>
                    <option value="I" <?php if(isset($request) && $request->input('ativo')  == 'I'): ?><?php echo e(' selected '); ?><?php else: ?> <?php endif; ?>>Inativo</option>
                </select>
            </div>
            <div class="form-group row">
                <div class="col-sm-5">
                    <button type="submit" class="btn btn-primary">Pesquisar</button>
                </div>
                <div class="col-sm-5">
                </div>
            </div>
        </form>
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
                    </tr>
                  </thead>
                  <tbody>
                  <?php if(isset($perfis)): ?>
                        <?php $__currentLoopData = $perfis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perfil): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>

                              <th scope="row">
                                <?php if(!empty($permissoes_liberadas) && in_array(1, $permissoes_liberadas)): ?>
                                    <a href=<?php echo e(URL::route($rotaAlterar, array('id' => $perfil->id ))); ?>><?php echo e($perfil->id); ?></a>
                                <?php else: ?>
                                    <?php echo e($perfil->id); ?>

                                <?php endif; ?>
                            </th>
                              <td><?php echo e($perfil->nome); ?></td>
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
            <form id="alterar" action="<?php echo e($rotaAlterar); ?>" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" method="post">
            <div class="form-group row">
                <div class="col-sm-2">
                <input type="hidden" id="id" name="id" class="form-control col-md-7 col-xs-12" readonly="true" value="<?php if(isset($perfis[0]->id)): ?><?php echo e($perfis[0]->id); ?><?php else: ?><?php echo e(''); ?><?php endif; ?>">
                </div>
            </div>
        <?php else: ?>
            <?php $__env->startSection('content_header'); ?>
                <h1 class="m-0 text-dark">Inclusão de <?php echo e($nome_tela); ?></h1>
            <?php $__env->stopSection(); ?>
            <form id="incluir" action="<?php echo e($rotaIncluir); ?>" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" method="post">
        <?php endif; ?>
            <?php echo csrf_field(); ?> <!--<?php echo e(csrf_field()); ?>-->
            <div class="form-group row">
                <label for="nome" class="col-sm-2 col-form-label  text-right">Nome</label>
                <div class="col-sm-6">
                <input type="text" class="form-control" id="nome"  name="nome" value="<?php if(isset($perfis[0]->nome)): ?><?php echo e($perfis[0]->nome); ?><?php else: ?><?php echo e(''); ?><?php endif; ?>">
                </div>
            </div>

            <div class="container-fluid">
                <label for="tela" class="col-sm-2 col-form-label ">Permissão de Telas</label>
                <div class="form-group row">
                        <?php $__currentLoopData = $telas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tela): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="card ml-2 p-4" style="width: 18rem;">
                                <div class="card-body">
                                    <input class="form-check-input" name="telas[]" value="<?php echo e($tela->id); ?>" type="checkbox" <?php if($tela->checked): ?><?php echo e('checked'); ?><?php endif; ?>/>
                                    <label class="form-check-label font-weight-bold" for="<?php echo e($tela->id); ?>"><?php echo e($tela->nome); ?></label>
                                    <?php $__currentLoopData = $acoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="form-check row">
                                            <input class="form-check-input" name="permissoes[]" value="<?php echo e($tela->id); ?>_<?php echo e($acao->id); ?>" type="checkbox"
                                                <?php if(!empty($perfis[0]->id) && !empty($permissoes[$perfis[0]->id][$tela->id]['acoes']) && in_array($acao->id, $permissoes[$perfis[0]->id][$tela->id]['acoes'])): ?> <?php echo e('checked'); ?> <?php endif; ?>/>
                                            <label class="form-check-label" for="<?php echo e($acao->id); ?>"><?php echo e($acao->nome); ?></label>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>

                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.extra-content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\juridico\resources\views\perfis.blade.php ENDPATH**/ ?>