

<?php $__env->startSection('title', env('APP_NAME')); ?>

<?php $__env->startSection('content_header'); ?>

        <div class="form-group row">
            <h1 class="m-0 text-dark col-sm-11 col-form-label">Conta</h1>
        </div>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../js/jquery.mask.js"></script>
    <script src="../js/bootstrap.4.6.2.js"></script>
    <script src="../js/main_custom.js"></script>
    

<?php $__env->stopSection(); ?>

<?php $__env->startSection('adminlte_css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/adminlte-custom.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content_top_nav_left'); ?>
    <?php echo $__env->make('layouts.navbar_left', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

        <div class="right_col" role="main">
            <form action="<?php echo e(route('settings.update')); ?>" method="post">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" value="<?php echo e($user->id ?? ''); ?>">
                <div class="container">

                    <?php if(session('success')): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <div class="row row-cols-md-3 g-3">
                        <div class="col-md-4">
                            <label for="pessoa" class="form-label">Nome*</label>
                            <input type="text" class="form-control" id="nome" name="nome" maxlength="200" required value="<?php echo e($user->name ?? ''); ?>">
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="A" <?php echo e(isset($user->status) && $user->status == 'A' ? 'selected' : ''); ?>>Ativo</option>
                                <option value="I" <?php echo e(isset($user->status) && $user->status == 'I' ? 'selected' : ''); ?>>Inativo</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <label for="email">Email</label>
                            <input type="text" class="form-control" id="email" readonly name="email" value='<?php echo e($user->email); ?>' placeholder="Email">
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-md-4">
                            <label for="senha">Senha</label>
                            <input type="password" class="form-control" id="senha" name="password" value='' placeholder="Senha">
                            <small class="form-text text-muted">Deixe em branco para manter a senha atual.</small>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="two_factor_enabled" name="two_factor_enabled" <?php echo e(!empty($user->two_factor_enabled) ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="two_factor_enabled">
                                    Ativar autenticação em 2 fatores (opcional)
                                </label>
                            </div>
                            <?php if(!empty($user->two_factor_enabled) && !empty($user->two_factor_secret)): ?>
                                <small class="form-text text-muted">
                                    Segredo 2FA ativo: <?php echo e(substr($user->two_factor_secret, 0, 8)); ?>********
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-8">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="lgpd_consent" name="lgpd_consent" <?php echo e(!empty($user->lgpd_consent_at) ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="lgpd_consent">
                                    Concordo com o tratamento de dados pessoais (LGPD)
                                </label>
                            </div>
                            <small class="form-text text-muted">Ao marcar esta opção, informe a finalidade do tratamento no campo abaixo.</small>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-8">
                            <label for="lgpd_purpose" class="form-label">Finalidade do tratamento (LGPD)</label>
                            <textarea class="form-control <?php $__errorArgs = ['lgpd_purpose'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="lgpd_purpose" name="lgpd_purpose" rows="3" maxlength="1000"><?php echo e(old('lgpd_purpose', $user->lgpd_purpose ?? '')); ?></textarea>
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

                    </div>
                    <div class="row mt-4 text-center">
                        <div class="col-md-12">
                            <button class="btn btn-danger mx-2" onclick="window.history.back();" type="button">Cancelar</button>
                            <button type="submit" class="btn btn-primary mx-2">Salvar</button>
                        </div>
                    </div>

                </div>

            </form>
        </div>
    </div>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.extra-content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\juridico\resources\views\settings.blade.php ENDPATH**/ ?>