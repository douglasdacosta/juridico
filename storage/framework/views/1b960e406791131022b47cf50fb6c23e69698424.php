
<div class="modal fade" id="<?php echo e($modalId); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo e($titulo); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form onsubmit="event.preventDefault(); <?php echo e($editar ? 'salvarEdicaoCompromisso()' : 'salvarNovoCompromisso()'); ?>;">
                    <?php if($editar): ?>
                        <input type="hidden" name="id">
                    <?php endif; ?>
                    <div class="form-group">
                        <label>Título</label>
                        <input type="text" class="form-control" name="titulo" required>
                    </div>
                    <div class="form-group">
                        <label>Tipo</label>
                        <select class="form-control" name="tipo" required>
                            <?php $__currentLoopData = $tipoOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $valor => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($valor); ?>"><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Data e hora</label>
                        <input type="datetime-local" class="form-control" name="data_hora" required>
                    </div>
                    <div class="form-group">
                        <label>Processo (opcional)</label>
                        <select class="form-control" name="processo_id">
                            <option value="">Nenhum</option>
                            <?php $__currentLoopData = $processosOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $numero): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($id); ?>"><?php echo e($numero); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Responsável</label>
                        <select class="form-control" name="responsavel_id">
                            <option value="">Eu mesmo</option>
                            <?php $__currentLoopData = $responsaveisOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $nome): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($id); ?>"><?php echo e($nome); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <?php if($editar): ?>
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="status">
                                <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $valor => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($valor); ?>"><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label>Observações</label>
                        <textarea class="form-control" name="observacoes" rows="3"></textarea>
                    </div>
                    <div class="modal-footer px-0">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /var/www/resources/views/formularios/agendaCompromissoFormulario.blade.php ENDPATH**/ ?>