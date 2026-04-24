<div class="col-md-12">
    <label for="Tipo pessoa" class="form-label">Arquivos</label>
    <div class="ml-2 col-md-2 ">
        <button type="button" class="form-control btn btn-success btn-sm adicionar_arquivo" id="adicionar_arquivo"><i class="fa fa-plus"></i> Adicionar arquivo</button>
    </div>
    <div class="ml-2 col-md-12 ">
        <small>Tipos permitidos: pdf, doc, docx, jpg, png, jpeg, zip. Tamanho máximo: 10MB</small>
    </div>


    <div class="input-group mb-3 div_arquivos">
                <span class="input-group-text" id="inputGroupFileAddon">Upload</span>
                <input type="file" class="form-control" name="modal_arquivos[]" id="modal_arquivos" aria-describedby="inputGroupFileAddon" aria-label="Upload">
    </div>

    {{-- Div com arquivos salvos --}}
    <div class="col-md-12 mt-2 div_arquivos_salvos">
        <label for="arquivos_salvos" class="form-label">Arquivos salvos</label>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nome do Arquivo</th>
                    <th>Tamanho</th>
                    <th>Data envio</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody id="arquivos_salvos">
                {{-- Conteúdo preenchido via JavaScript --}}
            </tbody>
        </table>

    </div>

</div>
