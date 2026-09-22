@extends('adminlte::page')

@section('title', env('APP_NAME'))

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
@stop

@section('content_top_nav_left')
    @include('layouts.navbar_left')
@stop

@section('content_header')
    <h1 class="m-0 text-dark">Fluxo de Caixa</h1>
@stop

@section('content')
@extends('layouts.extra-content')
    <div class="right_col" role="main">
        <form action="{{ route('relatorios.fluxo-caixa') }}" method="get" class="form-inline mb-3">
            <label for="meses" class="mr-2">Período</label>
            <select name="meses" id="meses" class="form-control mr-2" onchange="this.form.submit()">
                @foreach([3, 6, 12, 24] as $opcao)
                    <option value="{{ $opcao }}" {{ $meses == $opcao ? 'selected' : '' }}>Últimos {{ $opcao }} meses</option>
                @endforeach
            </select>
            <a href="{{ route('relatorios.fluxo-caixa.pdf', ['meses' => $meses]) }}" class="btn btn-outline-secondary" target="_blank">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
        </form>

        <div class="row mb-3">
            <div class="col-md-4">
                <div class="x_panel">
                    <div class="x_content text-center">
                        <small class="text-muted">Total de entradas</small>
                        <h3 class="text-success">R$ {{ number_format($totalEntradas, 2, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="x_panel">
                    <div class="x_content text-center">
                        <small class="text-muted">Total de saídas</small>
                        <h3 class="text-danger">R$ {{ number_format($totalSaidas, 2, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="x_panel">
                    <div class="x_content text-center">
                        <small class="text-muted">Saldo do período</small>
                        <h3 class="{{ ($totalEntradas - $totalSaidas) >= 0 ? 'text-success' : 'text-danger' }}">
                            R$ {{ number_format($totalEntradas - $totalSaidas, 2, ',', '.') }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="x_panel">
            <div class="x_title">
                <h4>Gráfico</h4>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <canvas id="graficoFluxoCaixa" height="90"></canvas>
            </div>
        </div>

        <div class="x_panel">
            <div class="x_content">
                <table class="table table-striped text-center">
                    <thead>
                        <tr>
                            <th>Mês</th>
                            <th>Entradas</th>
                            <th>Saídas</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($linhas as $linha)
                            <tr>
                                <td>{{ $linha['mes'] }}</td>
                                <td class="text-success">R$ {{ number_format($linha['entradas'], 2, ',', '.') }}</td>
                                <td class="text-danger">R$ {{ number_format($linha['saidas'], 2, ',', '.') }}</td>
                                <td class="{{ $linha['saldo'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    R$ {{ number_format($linha['saldo'], 2, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var linhas = @json($linhas);
            var canvas = document.getElementById('graficoFluxoCaixa');
            if (canvas && typeof Chart !== 'undefined') {
                new Chart(canvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: linhas.map(function (l) { return l.mes; }),
                        datasets: [
                            { label: 'Entradas', data: linhas.map(function (l) { return l.entradas; }), backgroundColor: '#28a745' },
                            { label: 'Saídas', data: linhas.map(function (l) { return l.saidas; }), backgroundColor: '#dc3545' },
                        ],
                    },
                    options: { plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } },
                });
            }
        });
    </script>
@stop
