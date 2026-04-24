
<div class="row mt-2">
    @include('formularios.tiposPessoasFormulario')
    <div class="col-md-6">
        <label for="nome_empresa"  class="form-label label_nome_empresa">Nome</label>
        <input type="text" class="form-control " id="modal_nome_empresa"
            name="nome_empresa" maxlength="180" value="">
    </div>
    <div class="col-md-3">
        <label for="documento" class="form-label label_documento">CPF </label>
        <input type="text" class="form-control cpf" id="modal_documento"
            name="documento" maxlength="14" value="">
    </div>
    <div class="col-md-3">
        <label for="modal_telefone_cliente" class="form-label">Telefone</label>
        <input type="text" class="form-control mask_phone" id="modal_telefone_cliente"
            name="telefone_cliente" value="">
    </div>
    <div class="col-md-5">
        <label for="modal_email_cliente" class="form-label">Email</label>
        <input type="email" class="form-control" id="modal_email_cliente" name="email"
            value="">
    </div>
    <div class="col-md-2">
        <label for="cep" class="form-label">CEP</label>
        <input type="text" class="form-control modal_cep cep" id="modal_cep"
            name="cep" maxlength="8" value="123">
    </div>
    <div class="col-md-4">
        <label for="endereco" class="form-label">Endereço</label>
        <input type="text" class="form-control  modal_endereco" id="modal_endereco"
            name="endereco" maxlength="500" value="">
    </div>
    <div class="col-md-1">
        <label for="numero" class="form-label">Número</label>
        <input type="text" class="form-control  modal_numero" id="modal_numero"
            name="numero" maxlength="20" value="">
    </div>
    <div class="col-md-3">
        <label for="bairro" class="form-label">Bairro</label>
        <input type="text" class="form-control  modal_bairro" id="modal_bairro"
            name="bairro" maxlength="100" value="">
    </div>
    <div class="col-md-2">
        <label for="complemento" class="form-label">Complemento</label>
        <input type="text" class="form-control  modal_complemento" id="modal_complemento"
            name="complemento" maxlength="100" value="">
    </div>
    <div class="col-md-4">
        <label for="cidade" class="form-label">Cidade</label>
        <input type="text" class="form-control modal_cidade" id="modal_cidade" name="cidade"
            maxlength="150" value="">
    </div>
    <div class="col-md-3">
        <label for="estado" class="form-label">Estado</label>
        <select class="form-control modal_estado" id="modal_estado" name="estado">
            <option value="0" selected>
                Selecione</option>
            @foreach ($estados as $estado)
                <option value="{{ $estado['id'] }}">{{ $estado['estado'] }}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div class="row mt-2">
    <div class="col-md-8">
        <label for="observacao" class="form-label">Observação</label>
        <textarea class="form-control" id="modal_observacoes" name="observacao" rows="2"></textarea>
    </div>
    <div class="col-md-2">
        <label for="created_at" class="form-label">Criado em</label>
        <input class="form-control mask_date" type="text" disabled id="modal_created_at" name="created_at" value="">
    </div>
</div>
<div class="row mt-2">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="modal_status"
            name="status" checked>
        <label class="form-check-label" for="status">Ativo</label>
    </div>
</div>

