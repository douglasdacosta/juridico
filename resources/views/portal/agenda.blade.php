@extends('portal.layout')

@section('content')
    <h4 class="mb-4">Audiências</h4>

    @forelse($audiencias as $audiencia)
        <div class="card portal-card shadow-sm mb-2">
            <div class="card-body py-2">
                <strong>{{ $audiencia->titulo }}</strong>
                <div class="small text-muted">
                    {{ $audiencia->data_hora->format('d/m/Y H:i') }}
                    @if($audiencia->processo)
                        — Processo {{ $audiencia->processo->numero_processo }}
                    @endif
                </div>
                <span class="badge badge-{{ $audiencia->status === 'concluido' ? 'success' : 'warning' }}">{{ $audiencia->status_label }}</span>
            </div>
        </div>
    @empty
        <p class="text-muted">Nenhuma audiência agendada.</p>
    @endforelse
@endsection
