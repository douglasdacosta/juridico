@extends('adminlte::page')

@section('title', 'CRM')

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
@stop

@section('content_top_nav_left')
    @include('layouts.navbar_left')
@stop

@if(isset($tela) and $tela == 'pesquisa')
    @section('content_header')
    <div class="form-group row">
        <h1 class="m-0 text-dark col-sm-11 col-form-label">Pesquisa de {{ $nome_tela }}</h1>
        <div class="col-sm-1">
            @include('layouts.nav-open-incluir', ['rotaIncluir => $rotaIncluir'])
        </div>
    </div>
    @stop
    @section('content')
    @extends('layouts.extra-content')
    <div class="right_col" role="main">

        <form id="filtro" action="perfis" method="get" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="">
            <div class="form-group row">
                <label for="id" class="col-sm-1 col-form-label text-right">Código</label>
                <div class="col-sm-2">
                    <input type="text" id="id" name="id" class="form-control" value="@if (isset($request) && $request->input('id') != ''){{$request->input('id')}}@else @endif">
                </div>
                <label for="nome" class="col-sm-1 col-form-label text-right">Nome</label>
                <div class="col-sm-3">
                    <input type="text" id="nome" name="nome" class="form-control" value="@if (isset($request) && trim($request->input('nome')) != ''){{$request->input('nome')}}@else @endif">
                </div>
                <label for="ativo" class="col-sm-1 col-form-label text-right">Situação</label>
                <select class="form-control col-md-1" id="ativo" name="ativo">
                    <option value="A" @if (isset($request) && $request->input('ativo') == 'A'){{ ' selected '}}@else @endif>Ativo</option>
                    <option value="I" @if (isset($request) && $request->input('ativo')  == 'I'){{ ' selected '}}@else @endif>Inativo</option>
                </select>
            </div>
            <div class="form-group row">
                <div class="col-sm-5">
                    <button type="submit" class="btn btn-primary">Pesquisar</button>
                </div>
                <div class="col-sm-5">
                </div>
            </div>
        </form>
        <div class="form-group">
          <label class="control-label col-md-3 col-sm-3 col-xs-12" for=""></label>
          <div class="col-md-12 col-sm-12 col-xs-12">
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
                    </tr>
                  </thead>
                  <tbody>
                  @if(isset($perfis))
                        @foreach ($perfis as $perfil)
                            <tr>

                              <th scope="row">
                                @if(!empty($permissoes_liberadas) && in_array(1, $permissoes_liberadas))
                                    <a href={{ URL::route($rotaAlterar, array('id' => $perfil->id )) }}>{{$perfil->id}}</a>
                                @else
                                    {{$perfil->id}}
                                @endif
                            </th>
                              <td>{{$perfil->nome}}</td>
                              </tr>
                        @endforeach
                    @endif
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
    </div>

    @stop
@else
@section('content')
        @if($tela == 'alterar')
            @section('content_header')
                <h1 class="m-0 text-dark">Alteração de {{ $nome_tela }}</h1>
            @stop
            <form id="alterar" action="{{$rotaAlterar}}" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" method="post">
            <div class="form-group row">
                <div class="col-sm-2">
                <input type="hidden" id="id" name="id" class="form-control col-md-7 col-xs-12" readonly="true" value="@if (isset($perfis[0]->id)){{$perfis[0]->id}}@else{{''}}@endif">
                </div>
            </div>
        @else
            @section('content_header')
                <h1 class="m-0 text-dark">Inclusão de {{ $nome_tela }}</h1>
            @stop
            <form id="incluir" action="{{$rotaIncluir}}" data-parsley-validate="" class="form-horizontal form-label-left" novalidate="" method="post">
        @endif
            @csrf <!--{{ csrf_field() }}-->
            <div class="form-group row">
                <label for="nome" class="col-sm-2 col-form-label  text-right">Nome</label>
                <div class="col-sm-6">
                <input type="text" class="form-control" id="nome"  name="nome" value="@if (isset($perfis[0]->nome)){{$perfis[0]->nome}}@else{{''}}@endif">
                </div>
            </div>

            <div class="container-fluid">
                <label for="tela" class="col-sm-2 col-form-label ">Permissão de Telas</label>
                <div class="form-group row">
                        @foreach ($telas as $tela)
                            <div class="card ml-2 p-4" style="width: 18rem;">
                                <div class="card-body">
                                    <input class="form-check-input" name="telas[]" value="{{$tela->id}}" type="checkbox" @if($tela->checked){{'checked'}}@endif/>
                                    <label class="form-check-label font-weight-bold" for="{{$tela->id}}">{{$tela->nome}}</label>
                                    @foreach ($acoes as $acao)
                                        <div class="form-check row">
                                            <input class="form-check-input" name="permissoes[]" value="{{$tela->id}}_{{$acao->id}}" type="checkbox"
                                                @if(!empty($perfis[0]->id) && !empty($permissoes[$perfis[0]->id][$tela->id]['acoes']) && in_array($acao->id, $permissoes[$perfis[0]->id][$tela->id]['acoes'])) {{'checked'}} @endif/>
                                            <label class="form-check-label" for="{{$acao->id}}">{{$acao->nome}}</label>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        @endforeach
                </div>
            </div>


            <div class="form-group row">
                <div class="col-sm-5">
                    <button class="btn btn-danger" onclick="window.history.back();" type="button">Cancelar</button>
                </div>
                <div class="col-sm-5">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </form>

    @stop
@endif
@section('js')
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="js/jquery.mask.js"></script>
    {{-- <script src="js/bootstrap.4.6.2.js"></script> --}}
    <script src="js/select2.min.js"></script>
    <script src="js/main_custom.js"></script>
    <script src="js/acoes.js"></script>
@stop
