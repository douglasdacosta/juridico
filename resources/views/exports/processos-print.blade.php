<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Exportação de Processos</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #222; }
        h1 { margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; vertical-align: top; }
        th { background: #f3f3f3; }
    </style>
</head>
<body onload="window.print()">
    <h1>Relatório de Processos</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Número</th>
                <th>Tipo</th>
                <th>Status</th>
                <th>Responsável</th>
                <th>Clientes</th>
                <th>Filiais</th>
            </tr>
        </thead>
        <tbody>
            @foreach($processos as $processo)
                <tr>
                    <td>{{ $processo->id }}</td>
                    <td>{{ $processo->numero_processo }}</td>
                    <td>{{ $processo->tipoAcao?->nome ?? ($processo->tipo_acao ?? '-') }}</td>
                    <td>{{ ucfirst($processo->status) }}</td>
                    <td>{{ $processo->responsavel->name ?? '-' }}</td>
                    <td>{{ $processo->clientes->pluck('nome')->implode(', ') }}</td>
                    <td>{{ $processo->filiais->pluck('nome')->implode(', ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
