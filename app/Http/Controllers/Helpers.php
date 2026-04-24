<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

class Helpers
{
    public static function formatFloatValue($value) {
        $value = preg_replace('/\,/', '.', preg_replace('/\./', '', $value));
        return number_format($value, 2, '.', '');
    }

    public static function formatRealFormat($value) {
        return number_format($value, 2, ',', '');
    }

    public function formatarCpfCnpj($numero) {
        // Remove qualquer caractere não numérico
        $numero = preg_replace('/\D/', '', $numero);

        // Verifica se é CPF (11 dígitos)
        if (strlen($numero) === 11) {
            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $numero);
        }
        // Verifica se é CNPJ (14 dígitos)
        elseif (strlen($numero) === 14) {
            return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $numero);
        }

        // Retorna o número original se não for CPF nem CNPJ
        return $numero;
    }

     /**
     * cONVERTE DATAHORA PARA O FORMATO 2024-10-10
     * @param mixed $value
     * @return string
     */
    public static function formatDate_dmY($value) {
        return Carbon::parse(str_replace('/', '-', $value))->format('Y-m-d');
    }

    /**
     * CONVERTE DATAHORA PARA O FORMATO 10/10/2024
     * @param mixed $value
     * @return string
     */
    public static function formatDate_ddmmYYYY($value) {
	    return Carbon::parse(str_replace('-', '/', $value))->format('d/m/Y');
    }


    /**
     * cONVERTE DATAHORA PARA O FORMATO 2024-10-10 10:10:00
     * @param mixed $value
     * @return string
     */
    public static function formatDate_dmYHis($value) {
        return Carbon::parse(str_replace('/', '-', $value))->format('Y-m-d HH:mm:ss');
    }


    /**
     * CONVERTE DATAHORA PARA O FORMATO 10/10/2024 10:10:00
     * @param mixed $value
     * @return string
     */
    public static function formatDate_datahoraminutosegundo($value) {
	    return Carbon::parse(str_replace('-', '/', $value))->format('d/m/Y H:i:s');
    }

    /**
     * CONVERTE DATAHORA PARA O FORMATO 10/10/2024 10:10:00
     * @param mixed $value
     * @return string
     */
    public static function formatDate_ddmmYYYYHHiiiss($value) {
	    return Carbon::parse(str_replace('-', '/', $value))->format('d/m/Y');
    }

    public static function getEstados() {
       return [
            ['id' =>1,
            'sigla'=>'AC',
             'estado'=>'Acre',
       ],
            ['id' =>2,
            'sigla'=>'AL',
             'estado'=>'Alagoas',
       ],
            ['id' =>3,
            'sigla'=>'AP',
             'estado'=>'Amapá',
       ],
            ['id' =>4,
            'sigla'=>'AM',
             'estado'=>'Amazonas',
       ],
            ['id' =>5,
            'sigla'=>'BA',
             'estado'=>'Bahia',
       ],
            ['id' =>6,
            'sigla'=>'CE',
             'estado'=>'Ceará',
       ],
            ['id' =>7,
            'sigla'=>'DF',
             'estado'=>'Distrito Federal',
       ],
            ['id' =>8,
            'sigla'=>'ES',
             'estado'=>'Espírito Santo',
       ],
            ['id' =>9,
            'sigla'=>'GO',
             'estado'=>'Goiás',
       ],
            ['id' =>10,
            'sigla'=>'MA',
             'estado'=>'Maranhão',
       ],
       [
            'id' =>11,
            'sigla'=>'MT',
             'estado'=>'Mato Grosso',
       ],
            ['id' =>12,
            'sigla'=>'MS',
             'estado'=>'Mato Grosso do Sul',
       ],
            ['id' =>13,
            'sigla'=>'MG',
             'estado'=>'Minas Gerais',
       ],
            ['id' =>14,
            'sigla'=>'PA',
             'estado'=>'Pará',
       ],
            ['id' =>15,
            'sigla'=>'PB',
             'estado'=>'Paraíba',
       ],
            ['id' =>16,
            'sigla'=>'PR',
             'estado'=>'Paraná',
       ],
            ['id' =>17,
            'sigla'=>'PE',
             'estado'=>'Pernambuco',
       ],
            ['id' =>18,
            'sigla'=>'PI',
             'estado'=>'Piauí',
       ],
            ['id' =>19,
            'sigla'=>'RJ',
             'estado'=>'Rio de Janeiro',
       ],
            ['id' =>20,
            'sigla'=>'RN',
             'estado'=>'Rio Grande do Norte',
       ],
            ['id' =>21,
            'sigla'=>'RS',
             'estado'=>'Rio Grande do Sul',
       ],
            ['id' =>22,
            'sigla'=>'RO',
             'estado'=>'Rondônia',
       ],
            ['id' =>23,
            'sigla'=>'RR',
             'estado'=>'Roraima',
       ],
            ['id' =>24,
            'sigla'=>'SC',
             'estado'=>'Santa Catarina',
       ],
            ['id' =>25,
            'sigla'=>'SP',
             'estado'=>'São Paulo',
       ],
            ['id' =>26,
            'sigla'=>'SE',
             'estado'=>'Sergipe',
       ],
           [  'id' =>27,
           'sigla'=>'TO',
           'estado'=> 	'Tocantins'
           ]
        ];
    }


/**
 * Limpa caracteres que não são números de uma string que representa um telefone.
 *
 * @param string $telefone
 * @return string
 */
    public static function somenteNumeros($telefone)
    {
        return preg_replace('/\D+/', '', $telefone);
    }

    /**
     * Transforma valor para salvar no banco como float 10,00 para 10.00
     *
     * @param string $numero
     * @return string
     */
    public static function formataSalvarFloat($valor)
    {
        if (is_null($valor) || $valor === '') {
            return null;
        }

        $valor = trim($valor);

        $valor = str_replace('.', '', $valor);

        $valor = str_replace(',', '.', $valor);

        return (float) $valor;

    }

    public static function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Calcula o valor formatado
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
