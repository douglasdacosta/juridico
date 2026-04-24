@extends('adminlte::auth.register')

@section('auth_header', 'Registrar nova conta')

@section('auth_body')
    <form action="{{ route('register') }}" method="post">
        @csrf

        {{-- Campo de código (só aparece se houver na URL) --}}
        @if (!empty(request('codigo')))
            <x-adminlte-input name="codigo_indicacao" label="Código de Indicação" value="{{ request()->query('codigo') }}" readonly fgroup-class="mb-3"/>
        @endif

        {{-- Campos padrão --}}
        <x-adminlte-input name="name" label="Nome" required fgroup-class="mb-3"/>
        <x-adminlte-input name="email" label="E-mail" type="email" required fgroup-class="mb-3"/>
        <x-adminlte-input name="chave_pix" label="Chave PIX" type="text" required fgroup-class="mb-3"/>
        <x-adminlte-input name="password" label="Senha" type="password" required fgroup-class="mb-3"/>
        <x-adminlte-input name="password_confirmation" label="Confirme a senha" type="password" required fgroup-class="mb-3"/>

        <button type="submit" class="btn btn-primary btn-block">Registrar</button>
    </form>
@endsection

@section('auth_footer')
    <p class="my-0">
        <a href="{{ route('login') }}">Já tem uma conta? Faça login</a>
    </p>
@endsection

{{-- array:5 [▼ // app/Http/Controllers/Auth/RegisterController.php:68
  "_token" => "b8f0zRcUa7PIHtnCcsf8OG3kuSL2V1GcdfhNqKzY"
  "name" => "desenvolvimento2"
  "email" => "desenvolvimento2@admin.com"
  "password" => "desenvolvimento2"
  "password_confirmation" => "desenvolvimento2"
] --}}
