@extends('adminlte::page')

@section('title', env('APP_NAME'))

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
@stop

@if(isset($tela) && $tela == 'pesquisa')
    @section('content_header')
        <div class="form-group row">
            <h1 class="m-0 text-dark col-sm-11 col-form-label">Pesquisa de {{ $nome_tela }}</h1>
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

            <form id="filtro" action="{{ route('tipos-acao') }}" method="get" class="form-horizontal form-label-left" novalidate>
                <div class="form-group row">
                    <label for="nome" class="col-sm-2 col-form-label text-right">Nome</label>
                    <div class="col-sm-4">
                        <input type="text" id="nome" name="nome" class="form-control" value="{{ $request->input('nome') ?? '' }}" placeholder="Digite o nome">
                    </div>

                    <label for="ativo" class="col-sm-2 col-form-label text-right">Status</label>
                    <div class="col-sm-2">
                        <select class="form-control" id="ativo" name="ativo">
                            <option value="">Todos</option>
                            <option value="1" {{ $request->filled('ativo') && $request->input('ativo') === '1' ? 'selected' : '' }}>Ativo</option>
                            <option value="0" {{ $request->filled('ativo') && $request->input('ativo') === '0' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>

                    <div class="col-sm-1">
                        <button type="submit" class="btn btn-primary">Pesquisar</button>
                    </div>
                </div>
            </form>

            <div class="x_panel">
                <div class="x_title">
                    <h4>Encontrados</h4>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table class="table table-striped text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Descrição</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tiposAcao as $tipo)
                                <tr>
                                    <th scope="row"><a href="{{ route($rotaAlterar, ['id' => $tipo->id]) }}">{{ $tipo->id }}</a></th>
                                    <td>{{ $tipo->nome }}</td>
                                    <td>{{ $tipo->descricao ?? '-' }}</td>
                                    <td>{{ $tipo->ativo ? 'Ativo' : 'Inativo' }}</td>
                                    <td>
                                        @if($tipo->ativo)
                                            <form action="{{ route('desativar-tipos-acao') }}" method="post" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $tipo->id }}">
                                                <button type="submit" class="btn btn-link btn-sm">Desativar</button>
                                            </form>
                                        @endif
                                        <a href="{{ route($rotaAlterar, ['id' => $tipo->id]) }}" class="btn btn-link btn-sm">Editar</a>
                                        <form action="{{ route('excluir-tipos-acao') }}" method="post" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $tipo->id }}">
                                            <button type="submit" class="btn btn-link btn-sm text-danger" onclick="return confirm('Deseja realmente excluir?')">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">Nenhum tipo de ação encontrado.</td>
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
        <h1 class="m-0 text-dark">{{ $tela == 'alterar' ? 'Alteração de' : 'Inclusão de' }} {{ $nome_tela }}</h1>
    @stop

    @section('content')
    @extends('layouts.extra-content')
        <div class="right_col" role="main">
            <form action="{{ $tela == 'alterar' ? route($rotaAlterar) : route($rotaIncluir) }}" method="post">
                @csrf
                @if($tela == 'alterar')
                    <input type="hidden" name="id" value="{{ $tipoAcao->id ?? '' }}">
                @endif

                <div class="container-fluid">
                    <div class="row mt-3">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome" value="{{ old('nome', $tipoAcao->nome ?? '') }}" required>
                                @error('nome')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-group">
                                <label for="descricao" class="form-label">Descrição</label>
                                <textarea class="form-control @error('descricao') is-invalid @enderror" id="descricao" name="descricao" rows="4">{{ old('descricao', $tipoAcao->descricao ?? '') }}</textarea>
                                @error('descricao')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
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

@section('js')
    <script src="js/jquery.mask.js"></script>
    <script src="js/main_custom.js"></script>
@stop
