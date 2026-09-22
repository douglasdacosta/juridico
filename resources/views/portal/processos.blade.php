@extends('portal.layout')

@section('content')
    <h4 class="mb-4">Meus Processos</h4>

    @forelse($processos as $processo)
        <div class="card portal-card shadow-sm mb-3">
            <div class="card-body">
                <h5 class="card-title mb-1">{{ $processo->numero_processo }}</h5>
                <p class="text-muted mb-2">{{ $processo->vara_tribunal }}</p>
                <span class="badge badge-{{ $processo->status === 'ativo' ? 'success' : 'secondary' }}">{{ ucfirst($processo->status) }}</span>
                <a href="{{ route('portal.processos.show', $processo->id) }}" class="btn btn-sm btn-outline-primary float-right">Ver andamentos</a>
            </div>
        </div>
    @empty
        <p class="text-muted">Nenhum processo vinculado ao seu cadastro.</p>
    @endforelse
@endsection
