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
            <h1 class="m-0 text-dark col-sm-11 col-form-label">Modelos de Documento</h1>
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

            <form action="{{ route('modelos-documento') }}" method="get" class="form-horizontal form-label-left" novalidate>
                <div class="form-group row">
                    <label for="nome" class="col-sm-1 col-form-label text-right">Nome</label>
                    <div class="col-sm-3">
                        <input type="text" id="nome" name="nome" class="form-control" value="{{ $request->input('nome') ?? '' }}">
                    </div>
                    <label for="tipo" class="col-sm-1 col-form-label text-right">Tipo</label>
                    <div class="col-sm-2">
                        <select class="form-control" id="tipo" name="tipo">
                            <option value="">Todos</option>
                            @foreach($tipoOptions as $valor => $label)
                                <option value="{{ $valor }}" {{ $request->input('tipo') === $valor ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-1">
                        <button type="submit" class="btn btn-primary">Pesquisar</button>
                    </div>
                </div>
            </form>

            <div class="x_panel">
                <div class="x_content">
                    <table class="table table-striped text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($modelos as $modelo)
                                <tr>
                                    <th scope="row"><a href="{{ route($rotaAlterar, ['id' => $modelo->id]) }}">{{ $modelo->id }}</a></th>
                                    <td>{{ $modelo->nome }}</td>
                                    <td>{{ $modelo->tipo_label }}</td>
                                    <td>
                                        <span class="badge {{ $modelo->ativo ? 'badge-success' : 'badge-secondary' }}">
                                            {{ $modelo->ativo ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route($rotaAlterar, ['id' => $modelo->id]) }}" class="btn btn-link btn-sm">Editar</a>
                                        @if($modelo->ativo)
                                            <form action="{{ route('desativar-modelos-documento') }}" method="post" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $modelo->id }}">
                                                <button type="submit" class="btn btn-link btn-sm text-danger" onclick="return confirm('Desativar este modelo?')">Desativar</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">Nenhum modelo de documento encontrado.</td>
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
        <h1 class="m-0 text-dark">{{ $tela == 'alterar' ? 'Alteração de' : 'Inclusão de' }} Modelo de Documento</h1>
    @stop

    @section('content')
    @extends('layouts.extra-content')
        <div class="right_col" role="main">
            <form action="{{ $tela == 'alterar' ? route($rotaAlterar) : route($rotaIncluir) }}" method="post">
                @csrf
                @if($tela == 'alterar')
                    <input type="hidden" name="id" value="{{ $modelo->id ?? '' }}">
                @endif

                <div class="container-fluid">
                    <div class="row row-cols-md-2 g-3 mt-2">
                        <div class="col-md-8">
                            <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome" value="{{ old('nome', $modelo->nome ?? '') }}">
                            @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                            <select class="form-control @error('tipo') is-invalid @enderror" id="tipo" name="tipo">
                                @foreach($tipoOptions as $valor => $label)
                                    <option value="{{ $valor }}" {{ (string) old('tipo', $modelo->tipo ?? '') === (string) $valor ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label for="corpo" class="form-label">Conteúdo <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('corpo') is-invalid @enderror" id="corpo" name="corpo" rows="15">{{ old('corpo', $modelo->corpo ?? '') }}</textarea>
                            @error('corpo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="form-text text-muted">
                                Placeholders disponíveis (substituídos automaticamente ao gerar o documento):
                                @foreach($placeholders as $placeholder)
                                    <code>{{ $placeholder }}</code>
                                @endforeach
                            </small>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-3 form-check">
                            @php $ativoChecked = old('ativo', isset($modelo) ? ($modelo->ativo ? '1' : '0') : '1'); @endphp
                            <input class="form-check-input" type="checkbox" id="ativo" name="ativo" value="1" {{ $ativoChecked == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="ativo">Ativo</label>
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
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        if (typeof tinymce !== 'undefined' && document.getElementById('corpo')) {
            tinymce.init({
                selector: '#corpo',
                height: 400,
                language: 'pt_BR',
                menubar: false,
                plugins: 'lists link table code',
                toolbar: 'undo redo | bold italic underline | bullist numlist | link table | code',
            });
        }
    </script>
@stop
