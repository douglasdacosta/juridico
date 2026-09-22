@extends('adminlte::page')

@section('title', env('APP_NAME'))

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
@stop

@section('content_top_nav_left')
    @include('layouts.navbar_left')
@stop

@section('content_header')
    <h1 class="m-0 text-dark">Inadimplência</h1>
@stop

@section('content')
@extends('layouts.extra-content')
    <div class="right_col" role="main">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="x_panel">
                    <div class="x_content text-center">
                        <small class="text-muted">Total a receber em atraso</small>
                        <h3 class="text-danger">R$ {{ number_format($totalReceberAtraso, 2, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="x_panel">
                    <div class="x_content text-center">
                        <small class="text-muted">Total a pagar em atraso</small>
                        <h3 class="text-danger">R$ {{ number_format($totalPagarAtraso, 2, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="x_panel">
            <div class="x_title">
                <h4>Itens vencidos (ordenados por dias de atraso)</h4>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <table class="table table-striped text-center">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Descrição</th>
                            <th>Valor</th>
                            <th>Vencimento</th>
                            <th>Dias em atraso</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($itens as $item)
                            <tr>
                                <td>{{ $item['tipo'] }}</td>
                                <td>{{ $item['descricao'] }}</td>
                                <td>R$ {{ number_format($item['valor'], 2, ',', '.') }}</td>
                                <td>{{ $item['vencimento']->format('d/m/Y') }}</td>
                                <td><span class="badge badge-danger">{{ $item['dias_atraso'] }} dia(s)</span></td>
                                <td><a href="{{ $item['link'] }}" class="btn btn-link btn-sm">Ver</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">Nenhum item em atraso. 🎉</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
