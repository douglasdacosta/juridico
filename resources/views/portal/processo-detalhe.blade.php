@extends('portal.layout')

@section('content')
    <a href="{{ route('portal.processos') }}" class="btn btn-link p-0 mb-3"><i class="fas fa-arrow-left"></i> Voltar</a>

    <div class="card portal-card shadow-sm mb-3">
        <div class="card-body">
            <h5 class="card-title">{{ $processo->numero_processo }}</h5>
            <p class="mb-1"><strong>Vara/Tribunal:</strong> {{ $processo->vara_tribunal }}</p>
            <p class="mb-0"><strong>Status:</strong> {{ ucfirst($processo->status) }}</p>
        </div>
    </div>

    <h5>Andamentos</h5>
    @forelse($processo->andamentos as $andamento)
        <div class="card portal-card shadow-sm mb-2">
            <div class="card-body py-2">
                <span class="badge badge-secondary">{{ ucfirst($andamento->tipo) }}</span>
                <small class="text-muted ml-2">{{ $andamento->data_andamento->format('d/m/Y') }}</small>
                <p class="mb-0 mt-2">{{ $andamento->descricao }}</p>
            </div>
        </div>
    @empty
        <p class="text-muted">Nenhum andamento registrado.</p>
    @endforelse
@endsection
