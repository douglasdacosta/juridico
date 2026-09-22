@extends('adminlte::page')

@section('title', env('APP_NAME'))

@section('adminlte_css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <style>
        .fc-event.compromisso-concluido { opacity: 0.5; text-decoration: line-through; }
        #calendario-agenda { background: #fff; padding: 10px; border-radius: 4px; }
    </style>
@stop

@section('content_top_nav_left')
    @include('layouts.navbar_left')
@stop

@if(isset($tela) && $tela == 'pesquisa')
    @section('content_header')
        <div class="form-group row">
            <h1 class="m-0 text-dark col-sm-11 col-form-label">Agenda</h1>
            <div class="col-sm-1">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalNovoCompromisso">
                    <i class="fas fa-plus"></i>
                </button>
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

            <div class="row">
                <div class="col-md-8">
                    <div id="calendario-agenda"></div>
                </div>
                <div class="col-md-4">
                    <form id="filtro" action="{{ route('agenda') }}" method="get" class="mb-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status" onchange="document.getElementById('filtro').submit();">
                                <option value="">Todos</option>
                                @foreach($statusOptions as $valor => $label)
                                    <option value="{{ $valor }}" {{ $request->input('status') === $valor ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tipo">Tipo</label>
                            <select class="form-control" id="tipo" name="tipo" onchange="document.getElementById('filtro').submit();">
                                <option value="">Todos</option>
                                @foreach($tipoOptions as $valor => $label)
                                    <option value="{{ $valor }}" {{ $request->input('tipo') === $valor ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    <div class="x_panel">
                        <div class="x_title">
                            <h4>Próximos compromissos</h4>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content" style="max-height: 500px; overflow-y: auto;">
                            @forelse($compromissos as $compromisso)
                                @php
                                    $corTipo = match($compromisso->tipo) {
                                        'audiencia' => '#dc3545',
                                        'prazo_fatal' => '#fd7e14',
                                        'reuniao' => '#0d6efd',
                                        default => '#6c757d',
                                    };
                                @endphp
                                <div class="mb-2 p-2 border rounded" id="compromisso-row-{{ $compromisso->id }}">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{ $compromisso->titulo }}</strong>
                                        <span class="badge" style="background-color: {{ $corTipo }}; color: #fff;">{{ $compromisso->tipo_label }}</span>
                                    </div>
                                    <div class="small text-muted">
                                        {{ $compromisso->data_hora->format('d/m/Y H:i') }}
                                        @if($compromisso->processo)
                                            — <a href="{{ route('alterar-processos', ['id' => $compromisso->processo_id]) }}">{{ $compromisso->processo->numero_processo }}</a>
                                        @endif
                                    </div>
                                    <div class="small">{{ $compromisso->status_label }}</div>
                                    <div class="mt-1">
                                        <button type="button" class="btn btn-link btn-sm p-0" onclick="editarCompromisso({{ $compromisso->id }})">Editar</button>
                                        @if($compromisso->status === 'pendente')
                                            <button type="button" class="btn btn-link btn-sm p-0 text-success" onclick="concluirCompromisso({{ $compromisso->id }})">Concluir</button>
                                        @endif
                                        <button type="button" class="btn btn-link btn-sm p-0 text-danger" onclick="excluirCompromisso({{ $compromisso->id }})">Excluir</button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center">Nenhum compromisso encontrado.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('formularios.agendaCompromissoFormulario', ['modalId' => 'modalNovoCompromisso', 'titulo' => 'Novo Compromisso', 'editar' => false])
        @include('formularios.agendaCompromissoFormulario', ['modalId' => 'modalEditarCompromisso', 'titulo' => 'Editar Compromisso', 'editar' => true])
    @stop
@endif

@section('js')
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/fullcalendar/dist/index.global.min.js') }}"></script>
    <script src="{{ asset('js/fullcalendar/packages/core/locales/pt-br.global.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendario-agenda');
            if (!calendarEl) return;

            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'pt-br',
                height: 650,
                headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,dayGridWeek,listWeek' },
                events: '{{ route('api.compromissos.feed') }}',
                eventClick: function (info) {
                    editarCompromisso(info.event.id);
                },
                dateClick: function (info) {
                    $('#modalNovoCompromisso').find('input[name="data_hora"]').val(info.dateStr + 'T09:00');
                    $('#modalNovoCompromisso').modal('show');
                }
            });

            calendar.render();
            window.agendaCalendar = calendar;
        });

        function salvarNovoCompromisso() {
            var form = $('#modalNovoCompromisso form');
            $.post('{{ route('incluir-agenda') }}', form.serialize(), function (resp) {
                if (resp.success) {
                    location.reload();
                }
            }).fail(function (xhr) {
                alert(xhr.responseJSON?.message || 'Erro ao salvar compromisso.');
            });
        }

        function editarCompromisso(id) {
            $.get('{{ route('alterar-agenda') }}', { id: id }, function (data) {
                var modal = $('#modalEditarCompromisso');
                modal.find('input[name="id"]').val(data.id);
                modal.find('input[name="titulo"]').val(data.titulo);
                modal.find('select[name="tipo"]').val(data.tipo);
                modal.find('input[name="data_hora"]').val(data.data_hora);
                modal.find('select[name="processo_id"]').val(data.processo_id);
                modal.find('select[name="responsavel_id"]').val(data.responsavel_id);
                modal.find('select[name="status"]').val(data.status);
                modal.find('textarea[name="observacoes"]').val(data.observacoes);
                modal.modal('show');
            }, 'json');
        }

        function salvarEdicaoCompromisso() {
            var form = $('#modalEditarCompromisso form');
            $.post('{{ route('alterar-agenda') }}', form.serialize(), function (resp) {
                if (resp.success) {
                    location.reload();
                }
            }).fail(function (xhr) {
                alert(xhr.responseJSON?.message || 'Erro ao salvar compromisso.');
            });
        }

        function concluirCompromisso(id) {
            if (!confirm('Marcar este compromisso como concluído?')) return;
            $.post('{{ route('concluir-agenda') }}', { id: id }, function () {
                location.reload();
            });
        }

        function excluirCompromisso(id) {
            if (!confirm('Deseja realmente excluir este compromisso?')) return;
            $.post('{{ route('excluir-agenda') }}', { id: id, _method: 'POST' }, function () {
                location.reload();
            });
        }
    </script>
@stop
