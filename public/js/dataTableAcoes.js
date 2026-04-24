$(function ($) {
    var pageLength = 30;
    if (typeof DataTable !== 'undefined') {

        // Tabela da tela de técnicos
        let table_servicos = new DataTable('#table_servicos', {
            responsive: true,
            "paging": true,
            "info": false,
            "lengthChange": false,
            "pageLength": pageLength,
             "ajax": {
                url: '/ajax/servicos', // rota que retorna JSON
                type: 'GET'
            },
            "columns": [
                { data: 'id', name: 'id' },
                { data: 'nome', name: 'nome' },
                { data: 'ativo', name: 'ativo' },
                { data: 'acoes', name: 'acoes', orderable: false, searchable: false }
            ],
            "language": {
                "sEmptyTable":     "Nenhum registro encontrado",
                "sInfo":           "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                "sInfoEmpty":      "Mostrando 0 até 0 de 0 registros",
                "sInfoFiltered":   "(Filtrados de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sInfoThousands":  ".",
                "sLengthMenu":     "Mostrar _MENU_ resultados por página",
                "sLoadingRecords": "Carregando...",
                "sProcessing":     "Processando...",
                "sZeroRecords":    "Nenhum registro encontrado",
                "sSearch":         "Pesquisar:",
                "oPaginate": {
                    "sNext":     "Próximo",
                    "sPrevious": "Anterior",
                    "sFirst":    "Primeiro",
                    "sLast":     "Último"
                },
                "oAria": {
                    "sSortAscending":  ": Ordenar colunas de forma ascendente",
                    "sSortDescending": ": Ordenar colunas de forma descendente"
                },
                "select": {
                    "rows": {
                        "_": "Selecionado %d linhas",
                        "0": "Nenhuma linha selecionada",
                        "1": "Selecionado 1 linha"
                    }
                }
            }
        });
        window.table_servicos = table_servicos;

        table_servicos.on('order.dt', function() {
            updateSortIcons();
        });

        function updateSortIcons() {

            $('th').removeClass('sorting_asc');
            $('th').removeClass('sorting_desc');

            $('th[aria-sort]').each(function() {
                var sortOrder = $(this).attr('aria-sort');
                if (sortOrder === 'ascending' ){
                    $(this).addClass('sorting_asc');
                }
                if (sortOrder === 'descending'){
                    $(this).addClass('sorting_desc');
                }
            });
        }
        updateSortIcons();


    // Tabela da tela de prestadores
    let table_prestadores = new DataTable('#table_prestadores', {
            responsive: true,
            "paging": true,
            "info": false,
            "lengthChange": false,
            "pageLength": pageLength,
             "ajax": {
                url: '/ajax/prestadores', // rota que retorna JSON
                type: 'GET'
            },
            "columns": [
                { data: 'id', name: 'id' },
                { data: 'nome', name: 'nome' },
                { data: 'ativo', name: 'ativo' },
                { data: 'acoes', name: 'acoes', orderable: false, searchable: false }
            ],
            "language": {
                "sEmptyTable":     "Nenhum registro encontrado",
                "sInfo":           "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                "sInfoEmpty":      "Mostrando 0 até 0 de 0 registros",
                "sInfoFiltered":   "(Filtrados de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sInfoThousands":  ".",
                "sLengthMenu":     "Mostrar _MENU_ resultados por página",
                "sLoadingRecords": "Carregando...",
                "sProcessing":     "Processando...",
                "sZeroRecords":    "Nenhum registro encontrado",
                "sSearch":         "Pesquisar:",
                "oPaginate": {
                    "sNext":     "Próximo",
                    "sPrevious": "Anterior",
                    "sFirst":    "Primeiro",
                    "sLast":     "Último"
                },
                "oAria": {
                    "sSortAscending":  ": Ordenar colunas de forma ascendente",
                    "sSortDescending": ": Ordenar colunas de forma descendente"
                },
                "select": {
                    "rows": {
                        "_": "Selecionado %d linhas",
                        "0": "Nenhuma linha selecionada",
                        "1": "Selecionado 1 linha"
                    }
                }
            }
        });

        window.table_prestadores = table_prestadores;
        table_prestadores.on('order.dt', function() {
            updateSortIcons();
        });


        // Tabela da tela de clientes
    let table_clientes = new DataTable('#table_clientes', {
            responsive: true,
            "paging": true,
            "info": false,
            "lengthChange": false,
            "pageLength": pageLength,
             "ajax": {
                url: '/ajax/clientes', // rota que retorna JSON
                type: 'GET'
            },
            "columns": [
                { data: 'id', name: 'id' },
                { data: 'nome_empresa', name: 'nome_empresa' },
                { data: 'nome', name: 'nome' },
                { data: 'ativo', name: 'ativo' },
                { data: 'acoes', name: 'acoes', orderable: false, searchable: false }
            ],
            "language": {
                "sEmptyTable":     "Nenhum registro encontrado",
                "sInfo":           "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                "sInfoEmpty":      "Mostrando 0 até 0 de 0 registros",
                "sInfoFiltered":   "(Filtrados de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sInfoThousands":  ".",
                "sLengthMenu":     "Mostrar _MENU_ resultados por página",
                "sLoadingRecords": "Carregando...",
                "sProcessing":     "Processando...",
                "sZeroRecords":    "Nenhum registro encontrado",
                "sSearch":         "Pesquisar:",
                "oPaginate": {
                    "sNext":     "Próximo",
                    "sPrevious": "Anterior",
                    "sFirst":    "Primeiro",
                    "sLast":     "Último"
                },
                "oAria": {
                    "sSortAscending":  ": Ordenar colunas de forma ascendente",
                    "sSortDescending": ": Ordenar colunas de forma descendente"
                },
                "select": {
                    "rows": {
                        "_": "Selecionado %d linhas",
                        "0": "Nenhuma linha selecionada",
                        "1": "Selecionado 1 linha"
                    }
                }
            }
        });

        window.table_clientes = table_clientes;
        table_clientes.on('order.dt', function() {
            updateSortIcons();
        });
        function updateSortIcons() {

            $('th').removeClass('sorting_asc');
            $('th').removeClass('sorting_desc');

            $('th[aria-sort]').each(function() {
                var sortOrder = $(this).attr('aria-sort');
                if (sortOrder === 'ascending' ){
                    $(this).addClass('sorting_asc');
                }
                if (sortOrder === 'descending'){
                    $(this).addClass('sorting_desc');
                }
            });
        }
        updateSortIcons();
    }


});
