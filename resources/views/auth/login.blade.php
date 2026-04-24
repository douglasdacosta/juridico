<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gestão Jurídica</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 90%;
            max-width: 1000px;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        /* Left side - Form */
        .login-form-section {
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-form-section h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .login-form-section p {
            color: #888;
            margin-bottom: 40px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group input::placeholder {
            color: #bbb;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            font-size: 13px;
        }

        .remember-forgot a {
            color: #667eea;
            text-decoration: none;
            transition: color 0.3s;
        }

        .remember-forgot a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .remember-forgot input[type="checkbox"] {
            cursor: pointer;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #f5c6cb;
            font-size: 14px;
        }

        /* Right side - Branding */
        .login-branding-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 60px 40px;
            text-align: center;
        }

        .logo-area {
            margin-bottom: 40px;
        }

        .logo-circle {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .logo-circle i {
            font-size: 50px;
            color: white;
        }

        .system-name {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 15px;
            letter-spacing: -0.5px;
        }

        .system-tagline {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 50px;
            line-height: 1.6;
        }

        .features-list {
            text-align: left;
            display: inline-block;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .feature-item i {
            font-size: 18px;
            margin-right: 15px;
            background: rgba(255, 255, 255, 0.2);
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
            }

            .login-branding-section {
                padding: 40px 30px;
            }

            .login-form-section {
                padding: 40px 30px;
            }

            .logo-circle {
                width: 80px;
                height: 80px;
            }

            .logo-circle i {
                font-size: 35px;
            }

            .system-name {
                font-size: 24px;
            }

            .features-list {
                display: none;
            }

            .system-tagline {
                margin-bottom: 20px;
            }
        }

        .form-footer {
            margin-top: 30px;
            text-align: center;
            color: #888;
            font-size: 13px;
        }

        .form-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left side - Login Form -->
        <div class="login-form-section">
            <div>
                <h1>Bem-vindo</h1>
                <p>Entre com seus dados para acessar o sistema</p>

                @if ($errors->any())
                    <div class="error-message">
                        <strong><i class="fas fa-exclamation-circle"></i> Erro!</strong><br>
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" id="email" name="email" placeholder="seu@email.com"
                            value="{{ old('email') }}" required autofocus autocomplete="email">
                    </div>

                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Senha</label>
                        <input type="password" id="password" name="password" placeholder="Digite sua senha"
                            required autocomplete="current-password">
                    </div>

                    <div class="remember-forgot">
                        <label style="display: flex; align-items: center; cursor: pointer; margin: 0;">
                            <input type="checkbox" name="remember" id="remember" style="width: 18px; height: 18px; cursor: pointer; margin-right: 8px;">
                            <span>Lembrar-me</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">Esqueci a senha</a>
                        @endif
                    </div>

                    <button type="submit" class="login-btn">
                        <i class="fas fa-sign-in-alt"></i> Entrar
                    </button>
                </form>

                <div class="form-footer">
                    Não tem conta? <a href="#">Entre em contato com o administrador</a>
                </div>
            </div>
        </div>

        <!-- Right side - Branding -->
        <div class="login-branding-section">
            <div class="logo-area">
                <div class="logo-circle">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <div class="system-name">Gestão Jurídica</div>
                <div class="system-tagline">
                    Sistema de Gestão de Processos Jurídicos
                </div>
            </div>

            <div class="features-list">
                <div class="feature-item">
                    <i class="fas fa-file-contract"></i>
                    <span>Gestão de Processos</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Controle de Andamentos</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-users"></i>
                    <span>Múltiplos Usuários</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-lock"></i>
                    <span>Segurança Garantida</span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
