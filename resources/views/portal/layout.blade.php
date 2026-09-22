<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal do Cliente — {{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.4.6.2.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <style>
        body { background: #f4f6f9; }
        .portal-navbar { background: #343a40; }
        .portal-navbar .navbar-brand, .portal-navbar .nav-link { color: #fff !important; }
        .portal-card { border-radius: 10px; }
        @media (max-width: 576px) {
            .container { padding-left: 12px; padding-right: 12px; }
        }
    </style>
    @yield('css')
</head>
<body>
    @auth('cliente')
        <nav class="navbar navbar-expand-md portal-navbar">
            <a class="navbar-brand" href="{{ route('portal.dashboard') }}"><i class="fas fa-balance-scale"></i> Portal do Cliente</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#portalNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="portalNav">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('portal.dashboard') }}">Início</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('portal.processos') }}">Meus Processos</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('portal.documentos') }}">Documentos</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('portal.financeiro') }}">Financeiro</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('portal.agenda') }}">Audiências</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <form action="{{ route('portal.logout') }}" method="post" class="form-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-light">Sair</button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>
    @endauth

    <div class="container py-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    @yield('js')
</body>
</html>
