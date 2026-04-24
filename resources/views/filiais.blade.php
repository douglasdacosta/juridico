@extends('adminlte::page')

@section('title', env('APP_NAME'))

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
@stop

@section('content_top_nav_left')
    @include('layouts.navbar_left')
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

            <form id="filtro" action="filiais" method="get" class="form-horizontal form-label-left" novalidate>
                <div class="form-group row">
                    <label for="nome" class="col-sm-1 col-form-label text-right">Nome</label>
                    <div class="col-sm-4">
                        <input type="text" id="nome" name="nome" class="form-control" value="{{ $request->input('nome') ?? '' }}">
                    </div>
                    <label for="ativo" class="col-sm-1 col-form-label text-right">Situação</label>
                    <div class="col-sm-2">
                        <select class="form-control" id="ativo" name="ativo">
                            <option value="">Todos</option>
                            <option value="1" {{ $request->input('ativo') === '1' ? 'selected' : '' }}>Ativo</option>
                            <option value="0" {{ $request->input('ativo') === '0' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-5">
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
                                <th>CNPJ</th>
                                <th>Endereço</th>
                                <th>Situação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($filiais ?? [] as $filial)
                                <tr>
                                    <th scope="row"><a href="{{ route($rotaAlterar, ['id' => $filial->id]) }}">{{ $filial->id }}</a></th>
                                    <td>{{ $filial->nome }}</td>
                                    <td>{{ $filial->cnpj }}</td>
                                    <td>{{ $filial->endereco }}</td>
                                    <td>{{ $filial->ativo ? 'Ativo' : 'Inativo' }}</td>
                                </tr>
                            @endforeach
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
            <form id="{{ $tela }}" action="{{ $tela == 'alterar' ? route($rotaAlterar) : route($rotaIncluir) }}" method="post">
                @csrf
                @if($tela == 'alterar')
                    <input type="hidden" name="id" value="{{ $filial->id ?? '' }}">
                @endif

                <div class="container">
                    <div class="row row-cols-md-3 g-3 mt-2">
                        <div class="col-md-4">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome" maxlength="255" value="{{ old('nome', $filial->nome ?? '') }}">
                            @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label for="cnpj" class="form-label">CNPJ</label>
                            <input type="text" class="form-control @error('cnpj') is-invalid @enderror" id="cnpj" name="cnpj" maxlength="20" value="{{ old('cnpj', $filial->cnpj ?? '') }}">
                            @error('cnpj')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-5">
                            <label for="endereco" class="form-label">Endereço</label>
                            <input type="text" class="form-control @error('endereco') is-invalid @enderror" id="endereco" name="endereco" maxlength="255" value="{{ old('endereco', $filial->endereco ?? '') }}">
                            @error('endereco')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row row-cols-md-3 g-3 mt-2">
                        <div class="col-md-2">
                            <label for="ativo" class="form-label">Ativo</label>
                            <select class="form-control" id="ativo" name="ativo">
                                <option value="1" {{ old('ativo', isset($filial) ? (int) $filial->ativo : 1) == 1 ? 'selected' : '' }}>Sim</option>
                                <option value="0" {{ old('ativo', isset($filial) ? (int) $filial->ativo : 1) == 0 ? 'selected' : '' }}>Não</option>
                            </select>
                        </div>
                    </div>

                    <div class="row row-cols-md-3 g-3 mt-4">
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
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="js/jquery.mask.js"></script>
    <script src="js/select2.min.js"></script>
    <script src="js/main_custom.js"></script>
@stop
