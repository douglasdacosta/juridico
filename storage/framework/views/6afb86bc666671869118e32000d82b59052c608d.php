<?php $__env->startSection('title', env('APP_NAME')); ?>

<?php $__env->startSection('adminlte_css'); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/adminlte-custom.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/select2.min.css')); ?>">
    <style>
        .fc-event.compromisso-concluido { opacity: 0.5; text-decoration: line-through; }
        #calendario-agenda { background: #fff; padding: 10px; border-radius: 4px; }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content_top_nav_left'); ?>
    <?php echo $__env->make('layouts.navbar_left', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php if(isset($tela) && $tela == 'pesquisa'): ?>
    <?php $__env->startSection('content_header'); ?>
        <div class="form-group row">
            <h1 class="m-0 text-dark col-sm-11 col-form-label">Agenda</h1>
            <div class="col-sm-1">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalNovoCompromisso">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
    <?php $__env->stopSection(); ?>

    <?php $__env->startSection('content'); ?>
    
        <div class="right_col" role="main">
            <?php if(session('success')): ?>
                <div class="alert alert-success"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <div id="calendario-agenda"></div>
                </div>
                <div class="col-md-4">
                    <form id="filtro" action="<?php echo e(route('agenda')); ?>" method="get" class="mb-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status" onchange="document.getElementById('filtro').submit();">
                                <option value="">Todos</option>
                                <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $valor => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($valor); ?>" <?php echo e($request->input('status') === $valor ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tipo">Tipo</label>
                            <select class="form-control" id="tipo" name="tipo" onchange="document.getElementById('filtro').submit();">
                                <option value="">Todos</option>
                                <?php $__currentLoopData = $tipoOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $valor => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($valor); ?>" <?php echo e($request->input('tipo') === $valor ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </form>

                    <div class="x_panel">
                        <div class="x_title">
                            <h4>Próximos compromissos</h4>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content" style="max-height: 500px; overflow-y: auto;">
                            <?php $__empty_1 = true; $__currentLoopData = $compromissos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compromisso): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $corTipo = match($compromisso->tipo) {
                                        'audiencia' => '#dc3545',
                                        'prazo_fatal' => '#fd7e14',
                                        'reuniao' => '#0d6efd',
                                        default => '#6c757d',
                                    };
                                ?>
                                <div class="mb-2 p-2 border rounded" id="compromisso-row-<?php echo e($compromisso->id); ?>">
                                    <div class="d-flex justify-content-between">
                                        <strong><?php echo e($compromisso->titulo); ?></strong>
                                        <span class="badge" style="background-color: <?php echo e($corTipo); ?>; color: #fff;"><?php echo e($compromisso->tipo_label); ?></span>
                                    </div>
                                    <div class="small text-muted">
                                        <?php echo e($compromisso->data_hora->format('d/m/Y H:i')); ?>

                                        <?php if($compromisso->processo): ?>
                                            — <a href="<?php echo e(route('alterar-processos', ['id' => $compromisso->processo_id])); ?>"><?php echo e($compromisso->processo->numero_processo); ?></a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="small"><?php echo e($compromisso->status_label); ?></div>
                                    <div class="mt-1">
                                        <button type="button" class="btn btn-link btn-sm p-0" onclick="editarCompromisso(<?php echo e($compromisso->id); ?>)">Editar</button>
                                        <?php if($compromisso->status === 'pendente'): ?>
                                            <button type="button" class="btn btn-link btn-sm p-0 text-success" onclick="concluirCompromisso(<?php echo e($compromisso->id); ?>)">Concluir</button>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-link btn-sm p-0 text-danger" onclick="excluirCompromisso(<?php echo e($compromisso->id); ?>)">Excluir</button>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <p class="text-muted text-center">Nenhum compromisso encontrado.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php echo $__env->make('formularios.agendaCompromissoFormulario', ['modalId' => 'modalNovoCompromisso', 'titulo' => 'Novo Compromisso', 'editar' => false], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('formularios.agendaCompromissoFormulario', ['modalId' => 'modalEditarCompromisso', 'titulo' => 'Editar Compromisso', 'editar' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php $__env->stopSection(); ?>
<?php endif; ?>

<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/fullcalendar/dist/index.global.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/fullcalendar/packages/core/locales/pt-br.global.min.js')); ?>"></script>
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
                events: '<?php echo e(route('api.compromissos.feed')); ?>',
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
            $.post('<?php echo e(route('incluir-agenda')); ?>', form.serialize(), function (resp) {
                if (resp.success) {
                    location.reload();
                }
            }).fail(function (xhr) {
                alert(xhr.responseJSON?.message || 'Erro ao salvar compromisso.');
            });
        }

        function editarCompromisso(id) {
            $.get('<?php echo e(route('alterar-agenda')); ?>', { id: id }, function (data) {
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
            $.post('<?php echo e(route('alterar-agenda')); ?>', form.serialize(), function (resp) {
                if (resp.success) {
                    location.reload();
                }
            }).fail(function (xhr) {
                alert(xhr.responseJSON?.message || 'Erro ao salvar compromisso.');
            });
        }

        function concluirCompromisso(id) {
            if (!confirm('Marcar este compromisso como concluído?')) return;
            $.post('<?php echo e(route('concluir-agenda')); ?>', { id: id }, function () {
                location.reload();
            });
        }

        function excluirCompromisso(id) {
            if (!confirm('Deseja realmente excluir este compromisso?')) return;
            $.post('<?php echo e(route('excluir-agenda')); ?>', { id: id, _method: 'POST' }, function () {
                location.reload();
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.extra-content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/agenda.blade.php ENDPATH**/ ?>