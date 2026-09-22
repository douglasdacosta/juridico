@extends('portal.layout')

@section('content')
    <h4 class="mb-4">Financeiro</h4>

    @forelse($lancamentos as $lancamento)
        <div class="card portal-card shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong>R$ {{ number_format($lancamento->valor_causa, 2, ',', '.') }}</strong>
                        <div class="small text-muted">{{ $lancamento->observacoes ?: 'Honorários' }}</div>
                    </div>
                    <span class="badge badge-{{ $lancamento->computed_status === 'pago' ? 'primary' : ($lancamento->computed_status === 'vencido' ? 'danger' : 'warning') }}">
                        {{ $lancamento->computed_status_label }}
                    </span>
                </div>

                @if($lancamento->parcelado && $lancamento->parcelas->count())
                    <hr>
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr><th>Parcela</th><th>Vencimento</th><th>Valor</th><th>Situação</th></tr>
                        </thead>
                        <tbody>
                            @foreach($lancamento->parcelas as $parcela)
                                <tr>
                                    <td>{{ $parcela->numero }}</td>
                                    <td>{{ $parcela->data_vencimento?->format('d/m/Y') }}</td>
                                    <td>R$ {{ number_format($parcela->valor, 2, ',', '.') }}</td>
                                    <td>{{ $parcela->data_pagamento ? 'Pago em ' . $parcela->data_pagamento->format('d/m/Y') : 'Pendente' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    @empty
        <p class="text-muted">Nenhum lançamento financeiro encontrado.</p>
    @endforelse
@endsection
