@extends('adminlte::page')

@section('title', env('APP_NAME'))

@section('adminlte_css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
@stop

@section('content_top_nav_left')
    @include('layouts.navbar_left')
@stop

@if(isset($tela) && $tela == 'pesquisa')
    @section('content_header')
        <div class="form-group row">
            <h1 class="m-0 text-dark col-sm-11 col-form-label">Contas a Pagar</h1>
            <div class="col-sm-1">
                @include('layouts.nav-open-incluir', ['rotaIncluir' => $rotaIncluir])
            </div>
        </div>
    @stop

    @section('content')
    @extends('layouts.extra-content')
        <div class="right_col" role="main">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="mb-3">
                <a href="{{ route('relatorios.fluxo-caixa') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-chart-line"></i> Fluxo de Caixa
                </a>
                <a href="{{ route('relatorios.inadimplencia') }}" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-exclamation-triangle"></i> Inadimplência
                </a>
            </div>

            <form id="filtro" action="{{ route('despesas') }}" method="get" class="form-horizontal form-label-left" novalidate>
                <div class="form-group row">
                    <label for="descricao" class="col-sm-1 col-form-label text-right">Descrição</label>
                    <div class="col-sm-3">
                        <input type="text" id="descricao" name="descricao" class="form-control" value="{{ $request->input('descricao') ?? '' }}">
                    </div>
                    <label for="status" class="col-sm-1 col-form-label text-right">Status</label>
                    <div class="col-sm-2">
                        <select class="form-control" id="status" name="status">
                            <option value="">Todos</option>
                            @foreach($statusOptions as $valor => $label)
                                <option value="{{ $valor }}" {{ $request->input('status') === $valor ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-1">
                        <button type="submit" class="btn btn-primary">Pesquisar</button>
                    </div>
                </div>
            </form>

            <div class="x_panel">
                <div class="x_title">
                    <h4>Encontradas</h4>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table class="table table-striped text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Descrição</th>
                                <th>Categoria</th>
                                <th>Filial</th>
                                <th>Valor</th>
                                <th>Vencimento</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($despesas ?? [] as $despesa)
                                <tr>
                                    <th scope="row"><a href="{{ route($rotaAlterar, ['id' => $despesa->id]) }}">{{ $despesa->id }}</a></th>
                                    <td>{{ $despesa->descricao }}</td>
                                    <td>{{ $despesa->categoria ?? '-' }}</td>
                                    <td>{{ $despesa->filial->nome ?? '-' }}</td>
                                    <td>R$ {{ number_format($despesa->valor, 2, ',', '.') }}</td>
                                    <td>{{ $despesa->data_vencimento->format('d/m/Y') }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match($despesa->computed_status) {
                                                'pago' => 'badge-primary',
                                                'atrasado' => 'badge-danger',
                                                default => 'badge-warning',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $despesa->computed_status_label }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route($rotaAlterar, ['id' => $despesa->id]) }}" class="btn btn-link btn-sm">Editar</a>
                                        @if($despesa->computed_status !== 'pago')
                                            <form action="{{ route('despesas.pagar') }}" method="post" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $despesa->id }}">
                                                <button type="submit" class="btn btn-link btn-sm text-success">Pagar</button>
                                            </form>
                                        @endif
                                        <form action="{{ route('excluir-despesas') }}" method="post" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $despesa->id }}">
                                            <button type="submit" class="btn btn-link btn-sm text-danger" onclick="return confirm('Deseja realmente excluir esta despesa?')">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">Nenhuma despesa encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @stop
@else
    @section('content_header')
        <h1 class="m-0 text-dark">{{ $tela == 'alterar' ? 'Alteração de' : 'Inclusão de' }} Despesa</h1>
    @stop

    @section('content')
    @extends('layouts.extra-content')
        <div class="right_col" role="main">
            <form action="{{ $tela == 'alterar' ? route($rotaAlterar) : route($rotaIncluir) }}" method="post">
                @csrf
                @if($tela == 'alterar')
                    <input type="hidden" name="id" value="{{ $despesa->id ?? '' }}">
                @endif

                <div class="container-fluid">
                    <div class="row row-cols-md-2 g-3 mt-2">
                        <div class="col-md-6">
                            <label for="descricao" class="form-label">Descrição <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('descricao') is-invalid @enderror" id="descricao" name="descricao" value="{{ old('descricao', $despesa->descricao ?? '') }}">
                            @error('descricao')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="categoria" class="form-label">Categoria</label>
                            <input type="text" class="form-control @error('categoria') is-invalid @enderror" id="categoria" name="categoria" value="{{ old('categoria', $despesa->categoria ?? '') }}" placeholder="Ex: Aluguel, Folha, Fornecedores...">
                            @error('categoria')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row row-cols-md-3 g-3 mt-2">
                        <div class="col-md-4">
                            <label for="valor" class="form-label">Valor <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control @error('valor') is-invalid @enderror" id="valor" name="valor" value="{{ old('valor', $despesa->valor ?? '') }}">
                            @error('valor')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="data_vencimento" class="form-label">Data de vencimento <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('data_vencimento') is-invalid @enderror" id="data_vencimento" name="data_vencimento" value="{{ old('data_vencimento', isset($despesa) && $despesa->data_vencimento ? $despesa->data_vencimento->format('Y-m-d') : '') }}">
                            @error('data_vencimento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="filial_id" class="form-label">Filial</label>
                            <select class="form-control @error('filial_id') is-invalid @enderror" id="filial_id" name="filial_id">
                                <option value="">Nenhuma</option>
                                @foreach($filiaisOptions as $id => $nome)
                                    <option value="{{ $id }}" {{ (string) old('filial_id', $despesa->filial_id ?? '') === (string) $id ? 'selected' : '' }}>{{ $nome }}</option>
                                @endforeach
                            </select>
                            @error('filial_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    @if(isset($despesa))
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label class="form-label">Status atual</label>
                                <div>
                                    @php
                                        $badgeClass = match($despesa->computed_status) {
                                            'pago' => 'badge-primary',
                                            'atrasado' => 'badge-danger',
                                            default => 'badge-warning',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $despesa->computed_status_label }}</span>
                                    @if($despesa->data_pagamento)
                                        <small class="text-muted ms-2">Pago em {{ $despesa->data_pagamento->format('d/m/Y') }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row row-cols-md-3 g-3 mt-2">
                        <div class="col-md-12">
                            <label for="observacoes" class="form-label">Observações</label>
                            <textarea class="form-control @error('observacoes') is-invalid @enderror" id="observacoes" name="observacoes" rows="4">{{ old('observacoes', $despesa->observacoes ?? '') }}</textarea>
                            @error('observacoes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-sm-5">
                            <button class="btn btn-danger" onclick="window.history.back();" type="button">Cancelar</button>
                        </div>
                        <div class="col-sm-5">
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @stop
@endif
