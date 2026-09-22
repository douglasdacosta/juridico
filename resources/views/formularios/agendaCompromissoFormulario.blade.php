{{-- Modal de criação/edição de compromisso da Agenda. Reaproveitado com $editar = true/false --}}
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $titulo }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form onsubmit="event.preventDefault(); {{ $editar ? 'salvarEdicaoCompromisso()' : 'salvarNovoCompromisso()' }};">
                    @if($editar)
                        <input type="hidden" name="id">
                    @endif
                    <div class="form-group">
                        <label>Título</label>
                        <input type="text" class="form-control" name="titulo" required>
                    </div>
                    <div class="form-group">
                        <label>Tipo</label>
                        <select class="form-control" name="tipo" required>
                            @foreach($tipoOptions as $valor => $label)
                                <option value="{{ $valor }}">{{ $label }}</option>
                            @endforeach
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
                            @foreach($processosOptions as $id => $numero)
                                <option value="{{ $id }}">{{ $numero }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Responsável</label>
                        <select class="form-control" name="responsavel_id">
                            <option value="">Eu mesmo</option>
                            @foreach($responsaveisOptions as $id => $nome)
                                <option value="{{ $id }}">{{ $nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($editar)
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="status">
                                @foreach($statusOptions as $valor => $label)
                                    <option value="{{ $valor }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
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
