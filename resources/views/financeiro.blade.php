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
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form id="filtro" action="{{ route('financeiro') }}" method="get" class="form-horizontal form-label-left" novalidate>
                <div class="form-group row">
                    <label for="cliente_id" class="col-sm-1 col-form-label text-right">Cliente</label>
                    <div class="col-sm-3">
                        <select class="form-control select2-ajax-clientes" id="cliente_id" name="cliente_id" style="width: 100%;">
                            <option value="">Todos</option>
                            @if($request->filled('cliente_id'))
                                @php
                                    $clienteSelecionado = \App\Models\Cliente::find($request->input('cliente_id'));
                                @endphp
                                @if($clienteSelecionado)
                                    <option value="{{ $clienteSelecionado->id }}" selected>{{ $clienteSelecionado->nome }}</option>
                                @endif
                            @endif
                        </select>
                    </div>
                    <label for="numero_processo" class="col-sm-2 col-form-label text-right">Nº Processo</label>
                    <div class="col-sm-2">
                        <input type="text" id="numero_processo" name="numero_processo" class="form-control" value="{{ $request->input('numero_processo') ?? '' }}">
                    </div>
                    <label for="status_pagamento" class="col-sm-1 col-form-label text-right">Status</label>
                    <div class="col-sm-2">
                        <select class="form-control" id="status_pagamento" name="status_pagamento">
                            <option value="">Todos</option>
                            @foreach($statusOptions as $valor => $label)
                                <option value="{{ $valor }}" {{ $request->input('status_pagamento') === $valor ? 'selected' : '' }}>{{ $label }}</option>
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
                    <h4>Encontrados</h4>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table class="table table-striped text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Processos</th>
                                <th>Valor da Causa</th>
                                <th>Honorários</th>
                                <th>Reembolso</th>
                                <th>Data Pagamento</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lancamentos ?? [] as $lancamento)
                                <tr>
                                    <th scope="row"><a href="{{ route($rotaAlterar, ['id' => $lancamento->id]) }}">{{ $lancamento->id }}</a></th>
                                    <td>{{ $lancamento->cliente->nome ?? '-' }}</td>
                                    <td>{{ $lancamento->processos->pluck('numero_processo')->implode(', ') }}</td>
                                    <td>R$ {{ number_format($lancamento->valor_causa, 2, ',', '.') }}</td>
                                    <td>{{ $lancamento->honorarios !== null ? 'R$ ' . number_format($lancamento->honorarios, 2, ',', '.') : '-' }}</td>
                                    <td>{{ $lancamento->reembolso !== null ? 'R$ ' . number_format($lancamento->reembolso, 2, ',', '.') : '-' }}</td>
                                    <td>{{ $lancamento->data_pagamento?->format('d/m/Y') ?? '-' }}</td>
                                    <td>
                                            @php
                                                $computed = $lancamento->computed_status;
                                                $badgeClass = match($computed) {
                                                    'pago' => 'badge-primary',
                                                    'vencido' => 'badge-danger',
                                                    'em_dia' => 'badge-success',
                                                    'nao_pago' => 'badge-warning',
                                                    default => 'badge-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $lancamento->computed_status_label }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route($rotaAlterar, ['id' => $lancamento->id]) }}" class="btn btn-link btn-sm">Editar</a>
                                        <form action="{{ route('excluir-financeiro') }}" method="post" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $lancamento->id }}">
                                            <button type="submit" class="btn btn-link btn-sm text-danger" onclick="return confirm('Deseja realmente excluir este lançamento?')">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">Nenhum lançamento financeiro encontrado.</td>
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
                    <input type="hidden" name="id" value="{{ $financeiro->id ?? '' }}">
                @endif

                <div class="container-fluid">
                    <div class="row row-cols-md-2 g-3 mt-2">
                        <div class="col-md-6">
                            <label for="cliente_id" class="form-label">Cliente <span class="text-danger">*</span></label>
                            <select class="form-control select2-ajax-clientes @error('cliente_id') is-invalid @enderror" id="cliente_id" name="cliente_id" style="width: 100%;">
                                <option value="">Selecione</option>
                                @php
                                    $clienteAtualId = old('cliente_id', $financeiro->cliente_id ?? '');
                                    $clienteAtual = $clienteAtualId ? \App\Models\Cliente::find($clienteAtualId) : null;
                                @endphp
                                @if($clienteAtual)
                                    <option value="{{ $clienteAtual->id }}" selected>{{ $clienteAtual->nome }}</option>
                                @endif
                            </select>
                            @error('cliente_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="processos" class="form-label">Processos vinculados <span class="text-danger">*</span></label>
                            <select multiple class="form-control select2-ajax-processos-multiple @error('processos') is-invalid @enderror" id="processos" name="processos[]" style="width: 100%;" {{ $clienteAtual ? '' : 'disabled' }}>
                                @if(old('processos'))
                                    @php
                                        $processosOld = \App\Models\Processo::whereIn('id', old('processos'))->get();
                                    @endphp
                                    @foreach($processosOld as $processo)
                                        <option value="{{ $processo->id }}" selected>{{ $processo->numero_processo }}</option>
                                    @endforeach
                                @elseif(isset($financeiro) && $financeiro->processos)
                                    @foreach($financeiro->processos as $processo)
                                        <option value="{{ $processo->id }}" selected>{{ $processo->numero_processo }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <small class="form-text text-muted">Selecione primeiro o cliente para listar os processos disponíveis.</small>
                            @error('processos')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    @if(isset($financeiro) && $financeiro->parcelas && $financeiro->parcelas->count())
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <h5>Parcelas</h5>
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Parcela</th>
                                            <th>Vencimento</th>
                                            <th>Valor</th>
                                            <th>Pagamento</th>
                                            <th>Data pagamento</th>
                                            <th>Valor pago</th>
                                            <th>Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($financeiro->parcelas as $parcela)
                                            <tr id="parcela-row-{{ $parcela->id }}">
                                                <td>{{ $parcela->numero }}</td>
                                                <td>{{ $parcela->data_vencimento?->format('d/m/Y') }}</td>
                                                <td>R$ {{ number_format($parcela->valor, 2, ',', '.') }}</td>
                                                <td class="parcela-status-{{ $parcela->id }}">
                                                    @if($parcela->data_pagamento)
                                                        Pago
                                                    @else
                                                        Pendente
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm mask-date parcela-data" data-parcela-id="{{ $parcela->id }}" value="{{ $parcela->data_pagamento ? $parcela->data_pagamento->format('d/m/Y') : '' }}">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm mask-money parcela-valor" data-parcela-id="{{ $parcela->id }}" value="{{ $parcela->valor_pago ? number_format($parcela->valor_pago, 2, ',', '.') : '' }}">
                                                </td>
                                                <td>
                                                    @if($parcela->status !== 'pago')
                                                        <button data-id="{{ $parcela->id }}" class="btn btn-sm btn-success btn-pagar-parcela">Salvar pagamento</button>
                                                    @else
                                                        <span class="text-muted">Pago</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                    </div>

                    <div class="row row-cols-md-3 g-3 mt-2">
                        <div class="col-md-4">
                            <label for="valor_causa" class="form-label">Valor da causa <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control @error('valor_causa') is-invalid @enderror" id="valor_causa" name="valor_causa" value="{{ old('valor_causa', $financeiro->valor_causa ?? '') }}">
                            @error('valor_causa')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="honorarios" class="form-label">Honorários</label>
                            <input type="number" step="0.01" min="0" class="form-control @error('honorarios') is-invalid @enderror" id="honorarios" name="honorarios" value="{{ old('honorarios', $financeiro->honorarios ?? '') }}">
                            @error('honorarios')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="reembolso" class="form-label">Reembolso</label>
                            <input type="number" step="0.01" min="0" class="form-control @error('reembolso') is-invalid @enderror" id="reembolso" name="reembolso" value="{{ old('reembolso', $financeiro->reembolso ?? '') }}">
                            @error('reembolso')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row row-cols-md-3 g-3 mt-2">
                        <div class="col-md-3">
                            <label for="data_pagamento" class="form-label">Data de pagamento</label>
                            <input type="text" class="form-control mask-date @error('data_pagamento') is-invalid @enderror" id="data_pagamento" name="data_pagamento" value="{{ old('data_pagamento', isset($financeiro) && $financeiro->data_pagamento ? $financeiro->data_pagamento->format('d/m/Y') : '') }}">
                            @error('data_pagamento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label for="valor_pago" class="form-label">Valor pago</label>
                            <input type="text" class="form-control mask-money @error('valor_pago') is-invalid @enderror" id="valor_pago" name="valor_pago" value="{{ old('valor_pago', isset($financeiro) && $financeiro->valor_pago ? number_format($financeiro->valor_pago, 2, ',', '.') : '') }}">
                            @error('valor_pago')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @if(isset($financeiro) && !($financeiro->parcelado ?? false) && ($financeiro->computed_status ?? '') !== 'pago')
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-success btn-pagar-financeiro" data-id="{{ $financeiro->id }}">Marcar como pago (à vista)</button>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            @php
                                $computed = $financeiro->computed_status ?? 'em_dia';
                                $badgeClass = match($computed) {
                                    'pago' => 'badge-primary',
                                    'vencido' => 'badge-danger',
                                    'em_dia' => 'badge-success',
                                    'nao_pago' => 'badge-warning',
                                    default => 'badge-secondary',
                                };
                            @endphp
                            <div>
                                <span class="badge {{ $badgeClass }}">{{ $financeiro->computed_status_label ?? ($statusOptions[$computed] ?? $computed) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row row-cols-md-3 g-3 mt-2">
                        <div class="col-md-2 form-check align-self-end">
                            @php $parceladoChecked = old('parcelado', isset($financeiro) ? ($financeiro->parcelado ? '1' : '0') : '0'); @endphp
                            <input class="form-check-input" type="checkbox" id="parcelado" name="parcelado" value="1" {{ $parceladoChecked == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="parcelado">Parcelado</label>
                        </div>
                        <div class="col-md-3">
                            <label for="numero_parcelas" class="form-label">Número de parcelas</label>
                            <input type="number" min="1" class="form-control @error('numero_parcelas') is-invalid @enderror" id="numero_parcelas" name="numero_parcelas" value="{{ old('numero_parcelas', $financeiro->numero_parcelas ?? '') }}">
                            @error('numero_parcelas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label for="valor_parcela" class="form-label">Valor da parcela</label>
                            <input type="number" step="0.01" min="0" class="form-control @error('valor_parcela') is-invalid @enderror" id="valor_parcela" name="valor_parcela" value="{{ old('valor_parcela', $financeiro->valor_parcela ?? '') }}">
                            @error('valor_parcela')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="data_primeira_parcela" class="form-label">Data primeira parcela</label>
                            <input type="date" class="form-control @error('data_primeira_parcela') is-invalid @enderror" id="data_primeira_parcela" name="data_primeira_parcela" value="{{ old('data_primeira_parcela', isset($financeiro) && $financeiro->data_primeira_parcela ? $financeiro->data_primeira_parcela->format('Y-m-d') : '') }}">
                            @error('data_primeira_parcela')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row row-cols-md-3 g-3 mt-2">
                        <div class="col-md-12">
                            <label for="observacoes" class="form-label">Observações</label>
                            <textarea class="form-control @error('observacoes') is-invalid @enderror" id="observacoes" name="observacoes" rows="4">{{ old('observacoes', $financeiro->observacoes ?? '') }}</textarea>
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

@section('js')
    <script src="js/jquery.mask.js"></script>
    <script src="js/main_custom.js"></script>
    <script src="js/select2.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function initProcessosSelect(clienteId) {
            $('.select2-ajax-processos-multiple').select2({
                ajax: {
                    url: '{{ route("api.processos.search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page || 1,
                            cliente_id: clienteId
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
                placeholder: 'Digite para buscar processos do cliente',
                allowClear: true,
                multiple: true,
                language: {
                    inputTooShort: function() { return 'Digite para buscar'; },
                    noResults: function() { return 'Nenhum processo encontrado'; },
                    searching: function() { return 'Buscando...'; },
                    loadingMore: function() { return 'Carregando mais resultados...'; }
                }
            });
        }

        $(function () {
            // Select2 AJAX para cliente (single)
            if ($('.select2-ajax-clientes').length) {
                $('.select2-ajax-clientes').select2({
                    ajax: {
                        url: '/api/clientes/search',
                        dataType: 'json',
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

            // Select2 AJAX para processos (multiple), dependente do cliente selecionado
            if ($('.select2-ajax-processos-multiple').length) {
                var clienteInicial = $('#cliente_id').val() || null;
                initProcessosSelect(clienteInicial);

                // Ao trocar o cliente, reinicia o select de processos e limpa a seleção anterior
                $('#cliente_id').on('change', function () {
                    var clienteId = $(this).val();

                    $('.select2-ajax-processos-multiple').val(null).trigger('change');
                    $('.select2-ajax-processos-multiple').prop('disabled', !clienteId);

                    if ($('.select2-ajax-processos-multiple').data('select2')) {
                        $('.select2-ajax-processos-multiple').select2('destroy');
                    }

                    initProcessosSelect(clienteId);
                });
            }

            // Toggle campos de parcelamento
            function toggleParcelamento() {
                var isParcelado = $('#parcelado').is(':checked');
                $('#numero_parcelas').prop('disabled', !isParcelado);
                $('#valor_parcela').prop('disabled', !isParcelado);
                $('#data_primeira_parcela').prop('disabled', !isParcelado);
            }

            $('#parcelado').on('change', function () {
                toggleParcelamento();
            });

            // Inicializa estado
            toggleParcelamento();
                // Inicializar máscaras
                if ($.fn.mask) {
                    $('.mask-date').mask('00/00/0000');
                    $('.mask-money').mask('#.##0,00', {reverse: true});
                }

                function dateDMYtoISO(dmy) {
                    if (!dmy) return null;
                    var parts = dmy.split('/');
                    if (parts.length !== 3) return null;
                    return parts[2] + '-' + parts[1] + '-' + parts[0];
                }

                function moneyMaskToFloat(masked) {
                    if (!masked) return null;
                    // 1.234.567,89 => 1234567.89
                    var num = masked.replace(/\./g, '').replace(/,/g, '.');
                    return num;
                }

            
                // Marcar parcela como paga (AJAX) com valor pago opcional
                $(document).on('click', '.btn-pagar-parcela', function (e) {
                    e.preventDefault();
                    var btn = $(this);
                    var parcelaId = btn.data('id');
                    var row = $('#parcela-row-' + parcelaId);
                    var dataPagamentoMask = row.find('.parcela-data').val();
                    var valorPagoMask = row.find('.parcela-valor').val();

                    var dataPagamento = dateDMYtoISO(dataPagamentoMask);
                    var valorPago = moneyMaskToFloat(valorPagoMask);

                    $.post('{{ route('financeiro.parcela.pagar') }}', { parcela_id: parcelaId, valor_pago: valorPago, data_pagamento: dataPagamento }, function (resp) {
                        if (resp.success) {
                            location.reload();
                        } else {
                            alert('Erro ao marcar parcela como paga');
                        }
                    }).fail(function () {
                        alert('Erro ao comunicar o servidor');
                    });
                });

            
                $(document).on('click', '.btn-pagar-financeiro', function (e) {
                    e.preventDefault();
                    var btn = $(this);
                    var financeiroId = btn.data('id');
                    var dataPagamentoMask = $('#data_pagamento').val();
                    var valorPagoMask = $('#valor_pago').val();

                    var dataPagamento = dateDMYtoISO(dataPagamentoMask);
                    var valorPago = moneyMaskToFloat(valorPagoMask);

                    $.post('{{ route('financeiro.pagar') }}', { financeiro_id: financeiroId, valor_pago: valorPago, data_pagamento: dataPagamento }, function (resp) {
                        if (resp.success) {
                            location.reload();
                        } else {
                            alert('Erro ao marcar lançamento como pago');
                        }
                    }).fail(function () {
                        alert('Erro ao comunicar o servidor');
                    });
                });
        });
    </script>
@stop
