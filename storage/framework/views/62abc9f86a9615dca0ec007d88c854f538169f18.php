<div class="col-md-4">
    <label for="banco" class="form-label">Banco</label>
    <select class="form-control" id="modal_banco" name="banco">
        <option value="0" >Selecione</option>
        <?php $__currentLoopData = $bancos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banco): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($banco['id']); ?>" ><?php echo e($banco['nome']); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
</div>

<div class="col-md-4">
    <label for="agencia" class="form-label">Agencia</label>
    <input type="text" class="form-control" id="modal_agencia" name="agencia" value="">
</div>

<div class="col-md-4">
    <label for="conta" class="form-label">Conta</label>
    <input type="text" class="form-control" id="modal_conta" name="conta" value="">
</div>

<div class="col-md-4">
    <label for="conta" class="form-label">Tipo conta</label>
    <select class="form-control" id="modal_tipo" name="tipo">
        <option value="0" >Selecione</option>
        <option value="1" >Corrente</option>
        <option value="2" >Poupanca</option>
    </select>
</div>

<div class="col-md-4">
    <label for="tipo chave pix" class="form-label">Tipo chave pix</label>
    <select class="form-control" id="modal_tipo_chave_pix" name="tipo_chave_pix">
        <option value="0" >Selecione</option>
        <option value="1" >CPF</option>
        <option value="2" >CNPJ</option>
        <option value="3" >Email</option>
        <option value="4" >Aleatória</option>
    </select>
</div>

<div class="col-md-4">
    <label for="chave_pix" class="form-label">Chave Pix</label>
    <input type="text" class="form-control" id="modal_chave_pix" name="chave_pix" value="">
</div>
<?php /**PATH C:\juridico\resources\views\formularios\prestadoresDadosBancariosFormulario.blade.php ENDPATH**/ ?>