@if (Route::currentRouteName() != 'incluir-clinicas')

    @if(isset($mostrarModalClinica) && $mostrarModalClinica )
            <!-- Modal -->
            <div class="modal fade" id="modalClinica" tabindex="-1" role="dialog" aria-labelledby="modalClinicaLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <form method="POST" action="{{ route('selecionar.clinica') }}">
                    @csrf
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Selecione o local de trabalho</h5>
                    </div>
                    <div class="modal-body">
                        <select name="clinica_id" class="form-control" required>
                            <option value="">-- Escolha o local --</option>                        
                            @foreach ($clinicas_atendentes->unique('clinica_id') as $clinica)
                                <option value="{{ $clinica->clinica_id }}">{{ $clinica->clinica_nome }}</option>
                            @endforeach                      
                        </select>
                    </div>
                    <div class="modal-footer">
                        @if( $clinicas_atendentes->isEmpty() )
                            
                            <a href="{{ route('incluir-clinicas') }}" class="btn btn-secondary">Cadastrar Clínicas</a>
                        @else
                            <button type="submit" class="btn btn-primary">Confirmar</button>
                        @endif
                    </div>
                    </div>
                </form>
            </div>
            </div>

            {{-- <script>
                $(document).ready(function() {
                    $('#modalClinica').modal('show');
                });
            </script> --}}
        @endif
    @endif