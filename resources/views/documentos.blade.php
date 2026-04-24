@extends('adminlte::page')

@section('title', env('APP_NAME'))

@section('adminlte_css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
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

            <form id="filtro" method="get" class="form-horizontal form-label-left" novalidate>
                <div class="form-group row">
                    <div class="col-sm-3">
                        <select class="form-control select2-ajax-clientes" name="cliente_id" style="width: 100%;">
                            <option value="">Cliente</option>
                            @if($request->filled('cliente_id'))
                                @php
                                    $clienteSelecionado = \App\Models\Cliente::find($request->input('cliente_id'));
                                @endphp
                                @if($clienteSelecionado)
                                    <option value="{{ $clienteSelecionado->id }}" selected>
                                        {{ $clienteSelecionado->nome }}{{ $clienteSelecionado->cpf ? ' (CPF: ' . $clienteSelecionado->cpf . ')' : '' }}
                                    </option>
                                @endif
                            @endif
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <input type="text" id="numero_processo" name="numero_processo" class="form-control mask_numero_processo" maxlength="25" placeholder="Processo" value="{{ $request->input('numero_processo') ?? '' }}">
                    </div>
                    <div class="col-sm-2">
                        <select class="form-control" name="ativo">
                            <option value="">Status</option>
                            <option value="1" {{ $request->input('ativo') === '1' ? 'selected' : '' }}>Ativo</option>
                            <option value="0" {{ $request->input('ativo') === '0' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-primary" onclick="fazerPesquisa()">Pesquisar</button>
                    </div>
                </div>
            </form>

            <div class="x_panel">
                <div class="x_title">
                    <h4>Documentos</h4>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table class="table table-striped text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Arquivo</th>
                                <th>Versão</th>
                                <th>Cliente</th>
                                <th>Processo</th>
                                <th>Andamento</th>
                                <th>Status</th>
                                    <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(($documentos ?? []) as $documento)
                                <tr>
                                    <td>{{ $documento->id }}</td>
                                    <td>{{ $documento->nome_original }}</td>
                                    <td>v{{ $documento->versao }}</td>
                                    <td>{{ $documento->cliente->nome ?? '-' }}</td>
                                    <td>{{ $documento->processo->numero_processo ?? '-' }}</td>
                                    <td>
                                        @if($documento->andamento)
                                            <span class="badge badge-info" title="{{ $documento->andamento->descricao }}">
                                                #{{ ucfirst($documento->andamento->tipo) }}
                                                {{ $documento->andamento->data_andamento->format('d/m/Y') }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $documento->ativo ? 'Ativo' : 'Inativo' }}
                                        @if($documento->ativo)
                                            <form action="{{ route('desativar-documentos') }}" method="post" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $documento->id }}">
                                                <button type="submit" class="btn btn-link btn-sm">Desativar</button>
                                            </form>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('preview-documentos', ['id' => $documento->id]) }}" class="btn btn-link btn-sm" target="_blank">Preview</a>
                                        <a href="{{ route($rotaAlterar, ['id' => $documento->id]) }}" class="btn btn-link btn-sm">Editar</a>
                                        @if($documento->ativo)
                                            <form action="{{ route('excluir-documentos') }}" method="post" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $documento->id }}">
                                                <button type="submit" class="btn btn-link btn-sm text-danger">Excluir</button>
                                            </form>
                                        @endif
                                    </td>
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
            <form action="{{ $tela == 'alterar' ? route($rotaAlterar) : route($rotaIncluir) }}" method="post" enctype="multipart/form-data">
                @csrf
                @if($tela == 'alterar')
                    <input type="hidden" name="id" value="{{ $documento->id }}">
                @endif
                @if(!empty($processoRetornoId))
                    <input type="hidden" name="processo_retorno_id" value="{{ $processoRetornoId }}">
                @endif
                <div class="container">
                    <div class="row row-cols-md-3 g-3 mt-2">
                        <div class="col-md-6">
                            <label for="arquivo" class="form-label">{{ $tela == 'alterar' ? 'Substituir arquivo (opcional)' : 'Arquivo' }}</label>
                            <input type="file" class="form-control @error('arquivo') is-invalid @enderror" id="arquivo" name="arquivo">
                            @error('arquivo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @if($tela == 'alterar')
                                <small class="text-muted">Atual: {{ $documento->nome_original }}</small>
                            @endif
                        </div>
                        <div class="col-md-3">
                            <label for="documento_base_id" class="form-label">Nova versão de</label>
                            <select class="form-control" id="documento_base_id" name="documento_base_id" {{ $tela == 'alterar' ? 'disabled' : '' }}>
                                <option value="">Novo documento</option>
                                @foreach(($documentosBaseOptions ?? []) as $documentoBase)
                                    <option value="{{ $documentoBase->id }}">#{{ $documentoBase->id }} - {{ $documentoBase->nome_original }} (v{{ $documentoBase->versao }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row row-cols-md-3 g-3 mt-2">
                        <div class="col-md-4">
                            <label for="cliente_id" class="form-label">Cliente</label>
                            <select class="form-control select2-ajax-clientes" id="cliente_id" name="cliente_id" style="width: 100%;">
                                <option value="">Selecione</option>
                                @if(old('cliente_id', $documento->cliente_id ?? ''))
                                    @php
                                        $clienteSelecionado = \App\Models\Cliente::find(old('cliente_id', $documento->cliente_id ?? ''));
                                    @endphp
                                    @if($clienteSelecionado)
                                        <option value="{{ $clienteSelecionado->id }}" selected>
                                            {{ $clienteSelecionado->nome }}{{ $clienteSelecionado->cpf ? ' (CPF: ' . $clienteSelecionado->cpf . ')' : '' }}
                                        </option>
                                    @endif
                                @endif
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="processo_id" class="form-label">Processo</label>
                            <select class="form-control" id="processo_id" name="processo_id">
                                <option value="">Selecione</option>
                                @foreach(($processosOptions ?? []) as $id => $numero)
                                    <option value="{{ $id }}" {{ (string) old('processo_id', $documento->processo_id ?? ($processoSelecionado ?? '')) === (string) $id ? 'selected' : '' }}>{{ $numero }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="andamento_id" class="form-label">Andamento</label>
                            <select class="form-control" id="andamento_id" name="andamento_id">
                                <option value="">Selecione</option>
                                @foreach(($andamentosOptions ?? []) as $id => $numero)
                                    <option value="{{ $id }}" {{ (string) old('andamento_id', $documento->andamento_id ?? '') === (string) $id ? 'selected' : '' }}>{{ $numero }}</option>
                                @endforeach
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
    <script>
        // Configurar cabeçalho CSRF para todas requisições AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function fazerPesquisa() {
            const clienteId = $('#filtro').find('select[name="cliente_id"]').val();
            const numeroProcesso = $('#filtro').find('input[name="numero_processo"]').val();
            const ativo = $('#filtro').find('select[name="ativo"]').val();

            // Construir URL com parâmetros
            let url = '{{ route("documentos") }}?';
            const params = [];
            if (clienteId) params.push('cliente_id=' + encodeURIComponent(clienteId));
            if (numeroProcesso) params.push('numero_processo=' + encodeURIComponent(numeroProcesso));
            if (ativo !== '') params.push('ativo=' + encodeURIComponent(ativo));

            url += params.join('&');

            // Navegar mantendo a sessão
            window.location.href = url;
        }

        function inicializarSelect2() {
            // Inicializar Select2 AJAX para clientes
            $('.select2-ajax-clientes').select2({
                ajax: {
                    url: '{{ route("api.clientes.search") }}',
                    dataType: 'json',
                    xhrFields: {
                        withCredentials: true
                    },
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results,
                            pagination: data.pagination
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0,
                placeholder: 'Digite para buscar cliente',
                allowClear: true,
                language: {
                    inputTooShort: function() { return 'Digite para buscar'; },
                    noResults: function() { return 'Nenhum cliente encontrado'; },
                    searching: function() { return 'Buscando...'; },
                    loadingMore: function() { return 'Carregando mais resultados...'; }
                }
            });
        }

        $(document).ready(function() {
            $('.mask_numero_processo').mask('0000000-00.0000.0.00.0000');
            inicializarSelect2();
        });
    </script>
@stop
