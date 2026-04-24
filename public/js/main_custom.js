// $(document).ready(function(){
//     $('.date').mask('00/00/0000');
//     $('.time').mask('00:00:00');
//     $('.date_time').mask('00/00/0000 00:00:00');
//     $('.cep').mask('00000-000');
//     $('.phone').mask('0000-0000');
//     $('.phone_with_ddd').mask('(00) 0000-0000');
//     $('.phone_us').mask('(000) 000-0000');
//     $('.mixed').mask('AAA 000-S0S');
//     $('.cpf').mask('000.000.000-00', {reverse: true});
//     $('.cnpj').mask('00.000.000/0000-00', {reverse: true});
//     $('.money').mask('000.000.000.000.000,00', {reverse: true});
//     $('.money2').mask("#.##0,00", {reverse: true});
//     $('.ip_address').mask('0ZZ.0ZZ.0ZZ.0ZZ', {
//       translation: {
//         'Z': {
//           pattern: /[0-9]/, optional: true
//         }
//       }
//     });
//     $('.ip_address').mask('099.099.099.099');
//     $('.percent').mask('##0,00%', {reverse: true});
//     $('.clear-if-not-match').mask("00/00/0000", {clearIfNotMatch: true});
//     $('.placeholder').mask("00/00/0000", {placeholder: "__/__/____"});
//     $('.fallback').mask("00r00r0000", {
//         translation: {
//           'r': {
//             pattern: /[\/]/,
//             fallback: '/'
//           },
//           placeholder: "__/__/____"
//         }
//       });
//     $('.selectonfocus').mask("00/00/0000", {selectOnFocus: true});

//   });

// Disable the sidebar accordion
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.querySelector('[data-widget="treeview"]');
    if (sidebar) {
        sidebar.setAttribute('data-accordion', 'false');
    }

    // Desabilitar o comportamento de accordion do AdminLTE
    $('[data-widget="treeview"]').each(function () {
        $(this).Treeview({accordion: false});
    });
});

$(function ($) {

    function manterMenusAbertos() {
        const menusFixos = ['Cadastros', 'Controles', 'Configurações'];
        const submenusFixos = ['Home', 'Cadastro de Perfis', 'Clientes', 'Filiais', 'Processos', 'Documentos', 'Tipos de Ação', 'Usuários'];

        $('.nav-sidebar > .nav-item').each(function () {
            const $item = $(this);
            const $linkPai = $item.children('.nav-link');
            const $tree = $item.children('.nav-treeview');

            if (!$tree.length) {
                return;
            }

            const textoMenu = $linkPai.find('p').clone().children().remove().end().text().trim();

            if (!menusFixos.includes(textoMenu)) {
                return;
            }

            $item.addClass('menu-open');
            $tree.css('display', 'block');

            // Impedir que o link pai feche o submenu
            $linkPai.off('click.treeview').off('click.menuFixo').on('click.menuFixo', function (event) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                $item.addClass('menu-open');
                $tree.css('display', 'block');
                return false;
            });
        });

        // Garantir que submenus fixos estejam sempre visíveis
        $('.nav-sidebar .nav-link').each(function() {
            const $link = $(this);
            const textoSubmenu = $link.find('p').clone().children().remove().end().text().trim();

            if (submenusFixos.includes(textoSubmenu)) {
                const $item = $link.parent();
                $item.addClass('nav-item-fixed');
                $item.addClass('menu-open');
                $item.children('.nav-treeview').css('display', 'block');
            }
        });
    }

    // Chamar a função após um pequeno delay para garantir que o AdminLTE foi inicializado
    setTimeout(manterMenusAbertos, 100);

    $(document).on('collapsed.lte.treeview', '.nav-sidebar', function () {
        manterMenusAbertos();
    });

    $(document).on('expanded.lte.treeview', '.nav-sidebar', function () {
        manterMenusAbertos();
    });


    //var baseUrl = '/proeffect/public'
    var baseUrl = ''

    $('.cep').mask('00000-000', {reverse: true});
    $('.sonumeros').mask('000000000000', {reverse: true});
    $('.mask_minutos').mask('00:00', {reverse: true});
    $('.mask_horas').mask('00:00:00', {reverse: true});
    $('.mask_valor').mask("000.000.000,00", {reverse: true});
    $('.mask_date').mask('00/00/0000');
    $('.mask_data_hora').mask('00/00/0000 00:00:00');
    $('.cpf').mask('000.000.000-00', {reverse: true});
    $('.cnpj').mask('00.000.000/0000-00', {reverse: true});
    $('.date').mask('00/00/0000');
    $('.tipo_pessoa[value="F"]').prop('checked', true);
    $('#id_dados_comerciais_incluir').hide();


    $(document).on('click', '.tipo_pessoa', function() {

        if($(this).val() === 'F') {
            $('.label_documento').text('CPF');
            $('.label_nome_empresa').text('Nome');
            $('#modal_alteracao #modal_documento').removeClass('cnpj').addClass('cpf');
            $('#modal_alteracao #modal_documento').unmask();
            $('#modal_alteracao #modal_documento').mask('000.000.000-00');
            $('.tab_dados_comerciais').hide();

            $('#modal_incluir #modal_documento').removeClass('cnpj').addClass('cpf');
            $('#modal_incluir #modal_documento').unmask();
            $('#modal_incluir #modal_documento').mask('000.000.000-00');
        } else {
            $('.tab_dados_comerciais').show();
            $('.label_documento').text('CNPJ');
            $('.label_nome_empresa').text('Razão Social');
            $('#modal_alteracao #modal_documento').removeClass('cpf').addClass('cnpj');
            $('#modal_alteracao #modal_documento').unmask();
            $('#modal_alteracao #modal_documento').mask('00.000.000/0000-00');
            $('#modal_incluir #modal_documento').removeClass('cpf').addClass('cnpj');
            $('#modal_incluir #modal_documento').unmask();
            $('#modal_incluir #modal_documento').mask('00.000.000/0000-00');
        }
    });

    var behavior = function (val) {
        return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
    },

    options = {
        onKeyPress: function (val, e, field, options) {
            field.mask(behavior.apply({}, arguments), options);
        }
    };
    $('.mask_phone').mask(behavior, options);


    if ($.fn.select2) {
        $('.default-select2').select2({
            placeholder: "",
            allowClear: false
        });

        // Foca automaticamente no campo de pesquisa ao clicar no select
        $('#default-select2').on('select2:open', function() {
            document.querySelector('.select2-search__field').focus();
        });

    }

    var validacao_cpf_cnpj = function (val) {
        return val.replace(/\D/g, '').length === 11 ? '000.000.000-00' : '00.000.000/0000-00';
    },
    options = {
        onKeyPress: function (val, e, field, options) {
            field.mask(validacao_cpf_cnpj.apply({}, arguments), options);
        },
        reverse: true,
        clearIfNotMatch: true
    };

    $('.mask_cpf_cnpj').mask(validacao_cpf_cnpj, options);

    $('.toast').hide();



    $(document).on("focus", ".mask_valor", function() {
        $(this).mask('###.###.##0,00', {reverse: true});
     });
    $(document).on("focus", ".data_pagamento", function() {
        $(this).mask('00/00/0000');
     });


    function abreAlertSuccess(texto, erro) {
        if(erro) {
            $('.toast').addClass('bg-danger')
        } else {
            $('.toast').addClass('bg-success')
        }
        $('.textoAlerta').text(texto);
        $('.toast').show();
        setTimeout(function () {
            $('.toast').hide('slow');
        }, 7000);
    };

    function validarCPF(cpf) {
        cpf = cpf.replace(/[^\d]/g, '');

        if (cpf.length !== 11) return false;

        if (/^(\d)\1+$/.test(cpf)) return false;

        let soma = 0;
        let resto;

        for (let i = 1; i <= 9; i++) {
            soma = soma + parseInt(cpf.substring(i-1, i)) * (11 - i);
        }
        resto = (soma * 10) % 11;
        if ((resto === 10) || (resto === 11)) resto = 0;
        if (resto !== parseInt(cpf.substring(9, 10))) return false;

        soma = 0;
        for (let i = 1; i <= 10; i++) {
            soma = soma + parseInt(cpf.substring(i-1, i)) * (12 - i);
        }
        resto = (soma * 10) % 11;
        if ((resto === 10) || (resto === 11)) resto = 0;
        if (resto !== parseInt(cpf.substring(10, 11))) return false;

        return true;
    }

    $(document).on('blur', '.mask_cpf_cnpj', function() {
        const cpf = $(this).val();
        const errorElement = $('#documento-error');

        if (!validarCPF(cpf)) {
            errorElement.show();
            $(this).addClass('is-invalid');
        } else {
            errorElement.hide();
            $(this).removeClass('is-invalid');
        }
    });


    const ufToName = {
    "AC":"Acre","AL":"Alagoas","AP":"Amapá","AM":"Amazonas","BA":"Bahia",
    "CE":"Ceará","DF":"Distrito Federal","ES":"Espírito Santo","GO":"Goiás",
    "MA":"Maranhão","MT":"Mato Grosso","MS":"Mato Grosso do Sul","MG":"Minas Gerais",
    "PA":"Pará","PB":"Paraíba","PR":"Paraná","PE":"Pernambuco","PI":"Piauí",
    "RJ":"Rio de Janeiro","RN":"Rio Grande do Norte","RS":"Rio Grande do Sul",
    "RO":"Rondônia","RR":"Roraima","SC":"Santa Catarina","SP":"São Paulo",
    "SE":"Sergipe","TO":"Tocantins"
    };



    // Quando o campo CEP perde o foco
    $('.modal_cep').on('blur', function () {
        let cep = $(this).val().replace(/\D/g, ''); // remove tudo que não for número

        if (cep.length === 8) {
            $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, function (dados) {
                if (!("erro" in dados)) {
                    let modalId = $(".modal.show").attr("id");

                    // Preenche os campos com os valores retornados

                    $('#' + modalId + ' #modal_endereco').val(dados.logradouro);
                    $('#' + modalId + ' #modal_bairro').val(dados.bairro);
                    $('#' + modalId + ' #modal_cidade').val(dados.localidade);
                    selectEstadoByUf(modalId, dados.uf);
                } else {
                    alert("CEP não encontrado!");
                    limpaCamposEndereco();
                }
            }).fail(function () {
                alert("Erro ao consultar o CEP!");
                limpaCamposEndereco();
            });
        } else {
            alert("CEP inválido! Digite 8 números.");
            limpaCamposEndereco();
        }
    });

    // Função para limpar os campos caso o CEP seja inválido
    function limpaCamposEndereco() {
        let modalId = $(".modal.show").attr("id");
        $('#' + modalId + ' #modal_endereco').val('');
        $('#' + modalId + ' #modal_bairro').val('');
        $('#' + modalId + ' #modal_cidade').val('');
        $('#' + modalId + ' #modal_estado').val('0');
    }

    function selectEstadoByUf(modalId, uf) {
        if (!uf) return;
        const nome = ufToName[uf.toUpperCase()];
        if (!nome) return;

        const $select = $('#' + modalId + ' #modal_estado');

        // Busca opção pelo texto (trim e comparar uppercase)
        const $opt = $select.find('option').filter(function () {
            return $(this).text().trim().toUpperCase() === nome.toUpperCase();
        });

        if ($opt.length) {
            $opt.prop('selected', true);
            $select.change(); // dispara change se você tiver listeners
        } else {
            // fallback: tenta selecionar pela sigla no value (caso já exista)
            $select.val(uf.toUpperCase()).change();
        }
        }


});

function compartilharLink() {
    const url = document.getElementById('link').value;
    const codigo = document.getElementById('codigo_indicacao').value;

    const texto = encodeURIComponent(
        `Ganhe descontos na compra de óculos e lente de contato na ÓTICA OFTALCLASS.\n`+
        `Apresente o código *${codigo}*  na hora da compra.\n\n`+
        `Ganhe com a compra dos amigos, cadastre seu PIX.\n\n`+
        `${url}`
    );

    // Detecta se é um dispositivo móvel
    const isMobile = /Android|iPhone|iPad|iPod|Windows Phone/i.test(navigator.userAgent);

    // Define a URL de compartilhamento
    const whatsappUrl = isMobile
        ? `https://wa.me/?text=${texto}`
        : `https://web.whatsapp.com/send?text=${texto}`;

    window.open(whatsappUrl, '_blank');
}

