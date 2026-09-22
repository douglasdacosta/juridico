<?php

namespace App\Support;

use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

/**
 * Geração de PDF real (via dompdf) com o timbre/logo do escritório,
 * substituindo as views antigas que dependiam de "imprimir pelo navegador".
 */
class PdfExporter
{
    public static function stream(string $view, array $data, string $filename): Response
    {
        $data['logoBase64'] = self::logoBase64();

        return Pdf::loadView($view, $data)->stream($filename);
    }

    /**
     * Retorna os bytes do PDF já renderizado, para salvar em disco (ex.: documento
     * gerado a partir de um modelo) em vez de enviar direto ao navegador.
     */
    public static function bytes(string $view, array $data): string
    {
        $data['logoBase64'] = self::logoBase64();

        return Pdf::loadView($view, $data)->output();
    }

    public static function logoBase64(): string
    {
        static $cache;

        if ($cache === null) {
            $path = public_path('img/img_logo.png');
            $cache = file_exists($path) ? base64_encode((string) file_get_contents($path)) : '';
        }

        return $cache;
    }
}
