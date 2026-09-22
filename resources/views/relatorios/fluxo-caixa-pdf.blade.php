<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Fluxo de Caixa</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #222; }
        h1 { margin-bottom: 16px; font-size: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th, td { border: 1px solid #ccc; padding: 6px; vertical-align: top; text-align: center; }
        th { background: #f3f3f3; }
        .totais td { font-weight: bold; background: #fafafa; }
        .positivo { color: #1e7e34; }
        .negativo { color: #bd2130; }
    </style>
</head>
<body>
    @include('exports._letterhead')

    <h1>Fluxo de Caixa — Últimos {{ $meses }} mês(es)</h1>

    <table>
        <thead>
            <tr>
                <th>Mês</th>
                <th>Entradas</th>
                <th>Saídas</th>
                <th>Saldo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($linhas as $linha)
                <tr>
                    <td>{{ $linha['mes'] }}</td>
                    <td>R$ {{ number_format($linha['entradas'], 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($linha['saidas'], 2, ',', '.') }}</td>
                    <td class="{{ $linha['saldo'] >= 0 ? 'positivo' : 'negativo' }}">
                        R$ {{ number_format($linha['saldo'], 2, ',', '.') }}
                    </td>
                </tr>
            @endforeach
            <tr class="totais">
                <td>Total</td>
                <td>R$ {{ number_format($totalEntradas, 2, ',', '.') }}</td>
                <td>R$ {{ number_format($totalSaidas, 2, ',', '.') }}</td>
                <td class="{{ ($totalEntradas - $totalSaidas) >= 0 ? 'positivo' : 'negativo' }}">
                    R$ {{ number_format($totalEntradas - $totalSaidas, 2, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>
