@extends('adminlte::page')

@section('title', 'Usuários')

@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('css/adminlte-custom.css') }}">
@stop

@section('content_header')
    <h1>Usuários do Sistema</h1>
@stop

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if($tela == 'pesquisa')
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Buscar Usuários</h3>
                        <div class="card-tools">
                            <a href="{{ route('incluir-usuarios') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Novo Usuário
                            </a>
                        </div>
                    </div>
                    <form method="GET" action="{{ route('usuarios') }}" class="card-body" id="formPesquisa">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nome">Nome</label>
                                    <input type="text" id="nome" name="nome" class="form-control"
                                        placeholder="Digite o nome" value="{{ $nome }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email" class="form-control"
                                        placeholder="Digite o email" value="{{ $email }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select id="status" name="status" class="form-control">
                                        <option value="">Todos</option>
                                        <option value="1" {{ $status === '1' ? 'selected' : '' }}>Ativo</option>
                                        <option value="0" {{ $status === '0' ? 'selected' : '' }}>Inativo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                                <a href="{{ route('usuarios') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Limpar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Resultados</h3>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Perfil</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($usuarios as $usuario)
                                    <tr>
                                        <td>{{ $usuario->name }}</td>
                                        <td>{{ $usuario->email }}</td>
                                        <td>{{ $usuario->perfil?->nome ?? 'N/A' }}</td>
                                        <td>{{ $usuario->status ? 'Ativo' : 'Inativo' }}</td>
                                        <td>
                                            @if($usuario->status)
                                                <form action="{{ route('desativar-usuarios') }}" method="post" style="display:inline;">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $usuario->id }}">
                                                    <button type="submit" class="btn btn-link btn-sm">Desativar</button>
                                                </form>
                                            @else
                                                <form action="{{ route('ativar-usuarios') }}" method="post" style="display:inline;">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $usuario->id }}">
                                                    <button type="submit" class="btn btn-link btn-sm">Ativar</button>
                                                </form>
                                            @endif
                                            <a href="{{ route('alterar-usuarios', ['id' => $usuario->id]) }}" class="btn btn-link btn-sm">Editar</a>
                                            <form action="{{ route('excluir-usuarios') }}" method="post" style="display:inline;">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $usuario->id }}">
                                                <button type="submit" class="btn btn-link btn-sm text-danger" onclick="return confirm('Deseja realmente excluir este usuário?')">Excluir</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Nenhum usuário encontrado</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($usuarios->hasPages())
                        <div class="card-footer">
                            {{ $usuarios->render() }}
                        </div>
                    @endif
                </div>

            @elseif($tela == 'incluir')
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Novo Usuário</h3>
                    </div>
                    <form method="POST" action="{{ route('incluir-usuarios') }}" class="card-body">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nome <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                        placeholder="Digite o nome" required value="{{ old('name') }}">
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                        placeholder="Digite o email" required value="{{ old('email') }}">
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Senha <span class="text-danger">*</span></label>
                                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Digite a senha (mínimo 6 caracteres)" required>
                                    @error('password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="perfil_acesso">Perfil <span class="text-danger">*</span></label>
                                    <select id="perfil_acesso" name="perfil_acesso" class="form-control @error('perfil_acesso') is-invalid @enderror" required>
                                        <option value="">Selecione um perfil</option>
                                        @foreach($perfis as $perfil)
                                            <option value="{{ $perfil->id }}" {{ old('perfil_acesso') == $perfil->id ? 'selected' : '' }}>
                                                {{ $perfil->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('perfil_acesso')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="">Selecione um status</option>
                                        <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Ativo</option>
                                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inativo</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Usuário
                            </button>
                            <a href="{{ route('usuarios') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>

            @elseif($tela == 'alterar')
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Editar Usuário</h3>
                    </div>
                    <form method="POST" action="{{ route('alterar-usuarios') }}" class="card-body">
                        @csrf
                        <input type="hidden" name="id" value="{{ $usuario->id }}">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nome <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                        placeholder="Digite o nome" required value="{{ old('name', $usuario->name) }}">
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                        placeholder="Digite o email" required value="{{ old('email', $usuario->email) }}">
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="perfil_acesso">Perfil <span class="text-danger">*</span></label>
                                    <select id="perfil_acesso" name="perfil_acesso" class="form-control @error('perfil_acesso') is-invalid @enderror" required>
                                        <option value="">Selecione um perfil</option>
                                        @foreach($perfis as $perfil)
                                            <option value="{{ $perfil->id }}" {{ old('perfil_acesso', $usuario->perfil_acesso) == $perfil->id ? 'selected' : '' }}>
                                                {{ $perfil->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('perfil_acesso')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="">Selecione um status</option>
                                        <option value="1" {{ old('status', $usuario->status) == '1' ? 'selected' : '' }}>Ativo</option>
                                        <option value="0" {{ old('status', $usuario->status) == '0' ? 'selected' : '' }}>Inativo</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Atualizar Usuário
                            </button>
                            <a href="{{ route('usuarios') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
@stop

@section('js')
    <script src="{{ asset('js/jquery.mask.js') }}"></script>
    <script src="{{ asset('js/main_custom.js') }}"></script>
@stop
