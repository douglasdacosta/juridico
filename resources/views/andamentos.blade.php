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

            <form id="filtro" action="andamentos" method="get" class="form-horizontal form-label-left" novalidate>
                <div class="form-group row">
                    <label for="numero_processo" class="col-sm-2 col-form-label text-right">Processo</label>
                    <div class="col-sm-4">
                        <input type="text" id="numero_processo" name="numero_processo" class="form-control mask_numero_processo" maxlength="25" value="{{ $request->input('numero_processo') ?? '' }}">
                    </div>
                    <label for="tipo" class="col-sm-1 col-form-label text-right">Tipo</label>
                    <div class="col-sm-3">
                        <select class="form-control" id="tipo" name="tipo">
                            <option value="">Todos</option>
                            @foreach(['peticao','audiencia','decisao','intimacao','recurso','outro'] as $tipo)
                                <option value="{{ $tipo }}" {{ $request->input('tipo') === $tipo ? 'selected' : '' }}>{{ ucfirst($tipo) }}</option>
                            @endforeach
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
                    <h4>Timeline de Andamentos</h4>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table class="table table-striped text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Processo</th>
                                <th>Tipo</th>
                                <th>Data</th>
                                <th>Responsável</th>
                                <th>Criado por</th>
                                <th>Descrição</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($andamentos ?? [] as $andamento)
                                <tr>
                                    <th scope="row"><a href="{{ route($rotaAlterar, ['id' => $andamento->id]) }}">{{ $andamento->id }}</a></th>
                                    <td>{{ $andamento->processo->numero_processo ?? '-' }}</td>
                                    <td>{{ ucfirst($andamento->tipo) }}</td>
                                    <td>{{ optional($andamento->data_andamento)->format('d/m/Y') }}</td>
                                    <td>{{ $andamento->usuario->name ?? '-' }}</td>
                                    <td>{{ $andamento->criador->name ?? '-' }}</td>
                                    <td style="max-width: 380px; white-space: normal;">{{ $andamento->descricao }}</td>
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
                    <input type="hidden" name="id" value="{{ $andamento->id ?? '' }}">
                @endif

                <div class="container">
                    <div class="row row-cols-md-3 g-3 mt-2">
                        <div class="col-md-4">
                            <label for="processo_id" class="form-label">Processo</label>
                            <select class="form-control @error('processo_id') is-invalid @enderror" id="processo_id" name="processo_id">
                                <option value="">Selecione</option>
                                @foreach(($processosOptions ?? []) as $id => $numero)
                                    <option value="{{ $id }}" {{ (string) old('processo_id', $andamento->processo_id ?? '') === (string) $id ? 'selected' : '' }}>{{ $numero }}</option>
                                @endforeach
                            </select>
                            @error('processo_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label for="tipo" class="form-label">Tipo</label>
                            <select class="form-control @error('tipo') is-invalid @enderror" id="tipo" name="tipo">
                                @foreach(['peticao','audiencia','decisao','intimacao','recurso','outro'] as $tipo)
                                    <option value="{{ $tipo }}" {{ old('tipo', $andamento->tipo ?? 'outro') === $tipo ? 'selected' : '' }}>{{ ucfirst($tipo) }}</option>
                                @endforeach
                            </select>
                            @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label for="data_andamento" class="form-label">Data</label>
                            <input type="date" class="form-control @error('data_andamento') is-invalid @enderror" id="data_andamento" name="data_andamento" value="{{ old('data_andamento', isset($andamento->data_andamento) ? $andamento->data_andamento->format('Y-m-d') : '') }}">
                            @error('data_andamento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row row-cols-md-3 g-3 mt-2">
                        <div class="col-md-8">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control @error('descricao') is-invalid @enderror" id="descricao" name="descricao" rows="5">{{ old('descricao', $andamento->descricao ?? '') }}</textarea>
                            @error('descricao')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <input type="hidden" name="usuario_id" value="{{ auth()->id() }}">
                    <input type="hidden" name="created_by" value="{{ auth()->id() }}">

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
    <script>
        $(document).ready(function() {
            $('.mask_numero_processo').mask('0000000-00.0000.0.00.0000');
        });
    </script>
@stop
