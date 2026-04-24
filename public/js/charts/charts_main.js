


  $(function ($) {
    
    function esperar(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    let formatador = new Intl.NumberFormat('pt-BR', {
        style: 'decimal',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    
    var line_data1 ;
    var DadosTicketMedioMensal ;
    var DadosSomaFaturamentoMensal ;
    var comparativoAnual1 ;
    var comparativoAnual2 ;
    var label_pacientes_categorias ;
    var valores_pacientes_categorias ;
    var label_faturamento_categorias ;
    var valores_faturamento_categorias ;
    var colunas = [];
    var valores = [];
    var tempoEspera = 500;

    function carregaDadosDashboards() {

        $.ajax({
            type: "POST",
            url: '/home-ajax',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                ano: $('#ano').val(),
                mes: $('#mes').val(),
                clinica: $('#clinica').val(),
                profissional: $('#profissional').val(),
                atendente: $('#atendente').val(),
            },
            success: function (data) {
        
                DadosTicketMedioMensal = Object.values(data.ticketMedioMensal);
                
                line_data1 = {
                    data: data.ticketMedioDiario,
                    color: '#3c8dbc'
                };

                DadosTicketMedioMensal = Object.values(data.ticketMedioMensal); //data.ticketMedioMensal;

                DadosSomaFaturamentoMensal =     Object.values(data.somaFaturamentoMensal);
                comparativoAnual1 =     Object.values(data.comparativoAnual1);
                comparativoAnual2 =     Object.values(data.comparativoAnual2);
                label_pacientes_categorias = Object.values(data.label_pacientes_categorias);
                valores_pacientes_categorias = Object.values(data.valores_pacientes_categorias);
                label_faturamento_categorias = Object.values(data.label_faturamento_categorias);
                valores_faturamento_categorias = Object.values(data.valores_faturamento_categorias);
                
                loadCharts();
            }
        });
    }

    carregaDadosDashboards();

    $(document).on('click', '#filtrar_dashboards', function (e) {
        carregaDadosDashboards()
    });

    let barChartInstance = barChartInstance2 = barChartInstance3 = null;
    let donutChartCanvas0 = donutChartCanvas1 = null;
    
    function loadCharts(){
        
        ticketMediDiario()
        ticketMedioMensal()
        faturamentoMensal()
        conparativoFaturamento()
        Quantidadepacientescategoria()
        faturamentoCaretogia()
    }    

    async function ticketMediDiario() {

        /*
        * LINE CHART
        * ----------
        */
        //LINE randomly generated data

        await esperar(tempoEspera);
        $('.spinner').hide();

        $.plot('#line-chart', [line_data1], {
        grid  : {
            hoverable  : true,
            borderColor: '#f3f3f3',
            borderWidth: 1,
            tickColor  : '#f3f3f3'
        },
        series: {
            shadowSize: 0,
            label: 'Ticket Medio Diario',
            lines     : {
            show: true
            },
            points    : {
            show: true
            }
        },
        lines : {
            fill : false,
            color: ['#3c8dbc', '#f56954']
        },
        yaxis : {
            show: true,
            tickFormatter: function(val, axis) {
                return 'R$ ' + formatador.format(val/ 100) ; // Adiciona "R$" e formata com duas casas decimais
            }
        },
        xaxis : {
            show: true,
            tickDecimals: 0,   // Apenas números inteiros
            minTickSize: 1,     // Incremento de 1 em 1
            tickSize: 1,
            tickFormatter: function(val, axis) {
                $valor = val < 10 ? '0' + val + '/03' : val + '/03';
                return $valor;
            }
        }
        })

        //Initialize tooltip on hover
        $('<div class="tooltip-inner" id="line-chart-tooltip"></div>').css({
        position: 'absolute',
        display : 'none',
        opacity : 0.8
        }).appendTo('body')
        $('#line-chart').bind('plothover', function (event, pos, item) {

        if (item) {
            var x = item.datapoint[0],
                y = item.datapoint[1]

            $('#line-chart-tooltip').html(item.series.label + ' do dia ' + x + ' de R$ ' + formatador.format(y/ 100))
            .css({
                top : item.pageY + 5,
                left: item.pageX + 5
            })
            .fadeIn(200)
        } else {
            $('#line-chart-tooltip').hide()
        }

        })
        /* END LINE CHART */
    }    
    


    async function ticketMedioMensal() {

        await esperar(tempoEspera);
        var areaChartData = {
            labels  : ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez', 'Total'],
            datasets: [
                {
                    label               : '',
                    backgroundColor     : 'rgba(60,141,188,0.9)',
                    borderColor         : 'rgba(60,141,188,0.8)',
                    pointRadius          : false,
                    pointColor          : '#3b8bba',
                    pointStrokeColor    : 'rgba(60,141,188,1)',
                    pointHighlightFill  : '#fff',
                    pointHighlightStroke: 'rgba(60,141,188,1)',
                    data                : DadosTicketMedioMensal
                }            
            ]
        }

        $('.spinner').hide();

        var barChartCanvas = $('#barChart').get(0).getContext('2d')
        // Destroi o gráfico antigo, se existir
        if (barChartInstance) {
            barChartInstance.destroy();
        }
        var barChartData = $.extend(true, {}, areaChartData)
        var temp0 = areaChartData.datasets[0]
        barChartData.datasets[0] = temp0

        var barChartOptions = {
        responsive              : true,
        maintainAspectRatio     : false,
        datasetFill             : false
        }

        barChartInstance = new Chart(barChartCanvas, {
            type: 'bar',
            data: barChartData,
            options: barChartOptions
        })

    }
    


    async function faturamentoMensal() {

        await esperar(tempoEspera);
        var areaChartData = {
            labels  : ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez', 'Total'],
            datasets: [
            {
                label               : '',
                backgroundColor     : 'rgba(60,141,188,0.9)',
                borderColor         : 'rgba(60,141,188,0.8)',
                pointRadius          : false,
                pointColor          : '#3b8bba',
                pointStrokeColor    : 'rgba(60,141,188,1)',
                pointHighlightFill  : '#fff',
                pointHighlightStroke: 'rgba(60,141,188,1)',
                data                : DadosSomaFaturamentoMensal
            }
            ]
        }

        $('.spinner').hide();



        var barChartCanvas = $('#barChart2').get(0).getContext('2d')
        
        var barChartData = $.extend(true, {}, areaChartData)
        var temp0 = areaChartData.datasets[0]
        barChartData.datasets[0] = temp0

        var barChartOptions = {
        responsive              : true,
        maintainAspectRatio     : false,
        datasetFill             : false
        }
        if (barChartInstance2) {
            barChartInstance2.destroy();
        }
        barChartInstance2  = new Chart(barChartCanvas, {
        type: 'bar',
        data: barChartData,
        options: barChartOptions
        })

    }
    
    

    async function conparativoFaturamento() {
        
        await esperar(tempoEspera);
        var areaChartData = {
            labels  : ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
            datasets: [
            {
                label               : '2024',
                backgroundColor     : 'rgba(60,141,188,0.9)',
                borderColor         : 'rgba(60,141,188,0.8)',
                pointRadius          : false,
                pointColor          : '#3b8bba',
                pointStrokeColor    : 'rgba(60,141,188,1)',
                pointHighlightFill  : '#fff',
                pointHighlightStroke: 'rgba(60,141,188,1)',
                data                : comparativoAnual1
            },
            {
                label               : '2023',
                backgroundColor     : 'rgba(210, 214, 222, 1)',
                borderColor         : 'rgba(210, 214, 222, 1)',
                pointRadius         : false,
                pointColor          : 'rgba(210, 214, 222, 1)',
                pointStrokeColor    : '#c1c7d1',
                pointHighlightFill  : '#fff',
                pointHighlightStroke: 'rgba(220,220,220,1)',
                data                : comparativoAnual2
            },
            ]
        }
        $('.spinner').hide();
        
        var barChartCanvas = $('#barChart3').get(0).getContext('2d')
        
        var barChartData = $.extend(true, {}, areaChartData)
        var temp0 = areaChartData.datasets[0]
        var temp1 = areaChartData.datasets[1]
        barChartData.datasets[0] = temp1
        barChartData.datasets[1] = temp0

        var barChartOptions = {
        responsive              : true,
        maintainAspectRatio     : false,
        datasetFill             : false
        }
        if (barChartInstance3) {
            barChartInstance3.destroy();
        }
        barChartInstance3  = new Chart(barChartCanvas, {
        type: 'bar',
        data: barChartData,
        options: barChartOptions
        })

    }
    


    async function  Quantidadepacientescategoria() {

        await esperar(tempoEspera);
        $('.spinner').hide();
        //-------------
        //- DONUT CHART -
        //-------------
        // Get context with jQuery - using jQuery's .get() method.
        var donutChartCanvas = $('#donutChart').get(0).getContext('2d')
        var donutData        = {
        labels: label_pacientes_categorias,
        datasets: [
            {
                data: valores_pacientes_categorias,
                backgroundColor : ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
            }
        ]
        }
        var donutOptions     = {
            maintainAspectRatio : false,
            responsive : true,
        }
        //Create pie or douhnut chart
        // You can switch between pie and douhnut using the method below.
        if (donutChartCanvas0) {
            donutChartCanvas0.destroy();
        }
        donutChartCanvas0 = new Chart(donutChartCanvas, {
        type: 'doughnut',
        data: donutData,
        options: donutOptions
        })

    }
    

    async function faturamentoCaretogia() {

        await esperar(tempoEspera);
        $('.spinner').hide();
        //-------------
        //- DONUT CHART -
        //-------------
        // Get context with jQuery - using jQuery's .get() method.
        var donutChartCanvas = $('#donutChart2').get(0).getContext('2d')
        var donutData        = {
        labels: label_faturamento_categorias,
        datasets: [
            {
                data: valores_faturamento_categorias,
                backgroundColor : ['#00c0ef', '#3c8dbc', '#d2d6de','#f56954', '#00a65a', '#f39c12' ],

            }
        ]
        }
        var donutOptions     = {
            maintainAspectRatio : false,
            responsive : true,
            tooltips: { // Para Chart.js v2.9.4
                callbacks: {
                    label: function (tooltipItem, data) {
                        let dataset = data.datasets[tooltipItem.datasetIndex];
                        let value = dataset.data[tooltipItem.index]; // Obtém o valor correto
                        let label = data.labels[tooltipItem.index]; // Obtém o valor correto
                        let valor = formatador.format(value/ 100);
                        return `${label}: R$ ${valor}`;
                    }
                }
            }
        }
        //Create pie or douhnut chart
        // You can switch between pie and douhnut using the method below.
        if (donutChartCanvas1) {
            donutChartCanvas1.destroy();
        }
        donutChartCanvas1  = new Chart(donutChartCanvas, {
            type: 'doughnut',
            data: donutData,
            options: donutOptions
        })

    }
    

  })

  