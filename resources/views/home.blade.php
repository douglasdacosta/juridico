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

@section('content_header')
  <h1 class="m-0 text-dark">Dashboard Jurídico</h1>
@stop

@section('content')
@extends('layouts.extra-content')
  <div class="right_col" role="main">
    <div class="row">
      <div class="col-lg-2 col-md-4 col-sm-6 col-12">
        <div class="small-box bg-primary">
          <div class="inner">
            <h3>{{ $kpis['total_processos'] ?? 0 }}</h3>
            <p>Processos</p>
          </div>
          <div class="icon"><i class="fa fa-balance-scale"></i></div>
        </div>
      </div>
      <div class="col-lg-2 col-md-4 col-sm-6 col-12">
        <div class="small-box bg-success">
          <div class="inner">
            <h3>{{ $kpis['processos_ativos'] ?? 0 }}</h3>
            <p>Ativos</p>
          </div>
          <div class="icon"><i class="fa fa-folder-open"></i></div>
        </div>
      </div>
      <div class="col-lg-2 col-md-4 col-sm-6 col-12">
        <div class="small-box bg-danger">
          <div class="inner">
            <h3>{{ $kpis['processos_encerrados'] ?? 0 }}</h3>
            <p>Encerrados</p>
          </div>
          <div class="icon"><i class="fa fa-folder"></i></div>
        </div>
      </div>
      <div class="col-lg-2 col-md-4 col-sm-6 col-12">
        <div class="small-box bg-info">
          <div class="inner">
            <h3>{{ $kpis['total_clientes'] ?? 0 }}</h3>
            <p>Clientes</p>
          </div>
          <div class="icon"><i class="fa fa-users"></i></div>
        </div>
      </div>
      <div class="col-lg-2 col-md-4 col-sm-6 col-12">
        <div class="small-box bg-warning">
          <div class="inner">
            <h3>{{ $kpis['total_andamentos'] ?? 0 }}</h3>
            <p>Andamentos</p>
          </div>
          <div class="icon"><i class="fa fa-clock-o"></i></div>
        </div>
      </div>
      <div class="col-lg-2 col-md-4 col-sm-6 col-12">
        <div class="small-box bg-secondary">
          <div class="inner">
            <h3>{{ $kpis['total_documentos'] ?? 0 }}</h3>
            <p>Documentos</p>
          </div>
          <div class="icon"><i class="fa fa-file-text-o"></i></div>
        </div>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-12">
        <div class="card bg-light">
          <div class="card-body">
            <h5 class="card-title mb-3">Acessos Rápidos</h5>
            <a href="{{ route('incluir-clientes') }}" class="btn btn-success mr-2">
              <i class="fa fa-plus"></i> Novo Cliente
            </a>
            <a href="{{ route('incluir-processos') }}" class="btn btn-primary mr-2">
              <i class="fa fa-plus"></i> Novo Processo
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="x_panel">
      <div class="x_title">
        <h4>Acesso rápido - Consulta de processos</h4>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <form action="{{ route('home') }}" method="get" class="form-horizontal form-label-left" novalidate>
          <div class="form-group row">
            <label for="cliente_id" class="col-sm-2 col-form-label text-right">Cliente</label>
            <div class="col-sm-4">
              <select class="form-control select2-ajax-clientes" id="cliente_id" name="cliente_id" style="width: 100%;">
                <option value="">Todos</option>
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

            <label for="numero_processo" class="col-sm-2 col-form-label text-right">Nº Processo</label>
            <div class="col-sm-4">
                            <input type="text" id="numero_processo" name="numero_processo" class="form-control mask_numero_processo" maxlength="25" value="{{ $request->input('numero_processo') ?? '' }}">
            </div>
          </div>

          <div class="form-group row">
            <div class="col-sm-12 text-right">
              <button type="submit" class="btn btn-primary">Consultar</button>
            </div>
          </div>
        </form>

        <table class="table table-striped text-center">
          <thead>
            <tr>
              <th>ID</th>
              <th>Número</th>
              <th>Status</th>
              <th>Cliente(s)</th>
              <th>Responsável</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($processos ?? []) as $processo)
              <tr>
                <th scope="row"><a href="{{ route('alterar-processos', ['id' => $processo->id]) }}">{{ $processo->id }}</a></th>
                <td>{{ $processo->numero_processo }}</td>
                <td>{{ ucfirst($processo->status) }}</td>
                <td>{{ $processo->clientes->pluck('nome')->implode(', ') }}</td>
                <td>{{ $processo->responsavel->name ?? '-' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5">Nenhum processo encontrado para os filtros informados.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@stop

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

    $(function () {
      $('.mask_numero_processo').mask('0000000-00.0000.0.00.0000');

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
    });
  </script>
@stop
