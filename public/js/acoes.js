$(function ($) {

    calcular();

    $(document).on('click', '.calcular', function (e) {
        calcular();
    });
    $(document).on('change', '.calcular', function (e) {
        calcular();
    });

    function calcular() {

        $table = $('#table_hospedagens tbody');
        $dias_viagem = parseInt($('#dias_viagem').val()) || 0;
        $gastros_extras = parseFloat(($('#gastros_extras').val() || '0').replace(',','.')) || 0;

        var soma_menor_valor = soma_maior_valor = soma_desconto_parceiro = soma_valor_cafe = total_com_desconto =0;

        $table.find('tr').each(function () {
            $this = $(this);

            if($this.find('.calcular').prop('checked') == false){
                return;
            }

            valor_cafe = $this.find('.valor_cafe').text().replace(',','.');
            soma_valor_cafe = soma_valor_cafe + (parseFloat(valor_cafe) || 0);

            desconto_parceiro = $this.find('.desconto_parceiro').text().replace(',','.');
            soma_desconto_parceiro = soma_desconto_parceiro + (parseFloat(desconto_parceiro) || 0);

            maior_valor = $this.find('.maior_valor').text().replace(',','.');
            soma_maior_valor = soma_maior_valor + (parseFloat(maior_valor) || 0);

            menor_valor = $this.find('.menor_valor').text().replace(',','.');
            soma_menor_valor = soma_menor_valor + (parseFloat(menor_valor) || 0);

        });



        total_menor_valor_com_desconto = soma_menor_valor - (soma_menor_valor * (soma_desconto_parceiro / 100));


        total_com_desconto = total_menor_valor_com_desconto + ($gastros_extras * $dias_viagem);

        $('#total_menor_valor').text('R$ ' + floatToBR(soma_menor_valor));
        $('#total_maior_valor').text('R$ ' + floatToBR(soma_maior_valor));
        $('#total_menor_valor_com_desconto').text('R$ ' + floatToBR(total_menor_valor_com_desconto));
        $('#total_com_desconto').text('R$ ' + floatToBR(total_com_desconto));

    }

    function floatToBR(valor) {
        if (valor === null || valor === undefined || valor === "") {
            return "";
        }

        return parseFloat(valor)
            .toFixed(2)               // 1710 → 1710.00
            .replace('.', ',')        // troca decimal
            .replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // milhares
    }

}); // fecha function($)
