@extends('portal.layout')

@section('content')
    <h4 class="mb-4">Olá, {{ $cliente->nome }}</h4>

    <div class="row">
        <div class="col-6 col-md-3 mb-3">
            <div class="card portal-card text-center shadow-sm">
                <div class="card-body">
                    <h3 class="mb-0">{{ $totalProcessos }}</h3>
                    <small class="text-muted">Processos</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card portal-card text-center shadow-sm">
                <div class="card-body">
                    <h3 class="mb-0">{{ $totalDocumentos }}</h3>
                    <small class="text-muted">Documentos</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card portal-card text-center shadow-sm">
                <div class="card-body">
                    <h3 class="mb-0">{{ $parcelasPendentes }}</h3>
                    <small class="text-muted">Parcelas pendentes</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="card portal-card text-center shadow-sm">
                <div class="card-body">
                    <h3 class="mb-0">{{ $proximosCompromissos->count() }}</h3>
                    <small class="text-muted">Próximas audiências</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card portal-card shadow-sm">
        <div class="card-header">Próximas audiências</div>
        <div class="card-body">
            @forelse($proximosCompromissos as $compromisso)
                <div class="mb-2 pb-2 border-bottom">
                    <strong>{{ $compromisso->titulo }}</strong>
                    <div class="small text-muted">{{ $compromisso->data_hora->format('d/m/Y H:i') }}</div>
                </div>
            @empty
                <p class="text-muted mb-0">Nenhuma audiência agendada.</p>
            @endforelse
        </div>
    </div>
@endsection
