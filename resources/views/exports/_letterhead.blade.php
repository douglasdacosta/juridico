{{-- Timbre reutilizado em todos os relatórios PDF gerados via dompdf --}}
<div style="text-align:center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px;">
    @if(!empty($logoBase64))
        <img src="data:image/png;base64,{{ $logoBase64 }}" style="height: 45px;">
    @endif
    <h2 style="margin: 6px 0 2px; font-size: 16px;">{{ config('adminlte.logo_img_alt', env('APP_NAME')) }}</h2>
    <div style="font-size: 9px; color: #666;">Relatório gerado em {{ now()->format('d/m/Y H:i') }}</div>
</div>
