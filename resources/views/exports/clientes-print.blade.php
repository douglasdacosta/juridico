<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Exportação de Clientes</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #222; }
        h1 { margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; vertical-align: top; }
        th { background: #f3f3f3; }
    </style>
</head>
<body onload="window.print()">
    <h1>Relatório de Clientes</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Telefone</th>
                <th>Status</th>
                <th>Cidade</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clientes as $cliente)
                <tr>
                    <td>{{ $cliente->id }}</td>
                    <td>{{ $cliente->nome }}</td>
                    <td>{{ $cliente->email }}</td>
                    <td>{{ $cliente->telefone }}</td>
                    <td>{{ $cliente->status }}</td>
                    <td>{{ $cliente->cidade }}</td>
                    <td>{{ $cliente->estado }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
