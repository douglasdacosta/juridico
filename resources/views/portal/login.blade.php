@extends('portal.layout')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-5 col-12">
            <div class="card portal-card shadow-sm mt-4">
                <div class="card-body p-4">
                    <h4 class="text-center mb-4"><i class="fas fa-balance-scale"></i> Portal do Cliente</h4>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $erro)
                                <div>{{ $erro }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('portal.login.submit') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="cpf">CPF</label>
                            <input type="text" class="form-control" id="cpf" name="cpf" value="{{ old('cpf') }}" autofocus required>
                        </div>
                        <div class="form-group">
                            <label for="password">Senha</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Entrar</button>
                    </form>

                    <p class="text-muted small mt-3 mb-0 text-center">
                        Não possui acesso? Solicite ao seu advogado responsável.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
