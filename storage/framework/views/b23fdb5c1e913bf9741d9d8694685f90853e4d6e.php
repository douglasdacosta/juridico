<?php if(Route::currentRouteName() != 'incluir-clinicas'): ?>

    <?php if(isset($mostrarModalClinica) && $mostrarModalClinica ): ?>
            <!-- Modal -->
            <div class="modal fade" id="modalClinica" tabindex="-1" role="dialog" aria-labelledby="modalClinicaLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <form method="POST" action="<?php echo e(route('selecionar.clinica')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Selecione o local de trabalho</h5>
                    </div>
                    <div class="modal-body">
                        <select name="clinica_id" class="form-control" required>
                            <option value="">-- Escolha o local --</option>                        
                            <?php $__currentLoopData = $clinicas_atendentes->unique('clinica_id'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clinica): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($clinica->clinica_id); ?>"><?php echo e($clinica->clinica_nome); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                      
                        </select>
                    </div>
                    <div class="modal-footer">
                        <?php if( $clinicas_atendentes->isEmpty() ): ?>
                            
                            <a href="<?php echo e(route('incluir-clinicas')); ?>" class="btn btn-secondary">Cadastrar Clínicas</a>
                        <?php else: ?>
                            <button type="submit" class="btn btn-primary">Confirmar</button>
                        <?php endif; ?>
                    </div>
                    </div>
                </form>
            </div>
            </div>

            
        <?php endif; ?>
    <?php endif; ?><?php /**PATH C:\juridico\resources\views\layouts\extra-content.blade.php ENDPATH**/ ?>