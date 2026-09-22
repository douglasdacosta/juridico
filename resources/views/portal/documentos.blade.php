@extends('portal.layout')

@section('content')
    <h4 class="mb-4">Documentos</h4>

    @forelse($documentos as $documento)
        <div class="card portal-card shadow-sm mb-2">
            <div class="card-body d-flex justify-content-between align-items-center py-2">
                <div>
                    <strong>{{ $documento->nome_original }}</strong>
                    <div class="small text-muted">
                        Processo: {{ $documento->processo->numero_processo ?? '-' }} · v{{ $documento->versao }}
                    </div>
                </div>
                <a href="{{ route('portal.documentos.preview', $documento->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                    <i class="fas fa-eye"></i> Ver
                </a>
            </div>
        </div>
    @empty
        <p class="text-muted">Nenhum documento disponível.</p>
    @endforelse
@endsection
