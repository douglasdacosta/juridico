<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #222; }
    </style>
</head>
<body>
    @include('exports._letterhead')

    <div>{!! $conteudo !!}</div>
</body>
</html>
