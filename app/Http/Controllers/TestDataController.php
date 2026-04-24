<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TestDataController extends Controller
{
    /**
     * Adiciona usuários de teste (APENAS EM DESENVOLVIMENTO)
     * Acesse: /add-test-users
     */
    public function addTestUsers()
    {
        if (!env('APP_DEBUG')) {
            return response('Not allowed in production', 403);
        }

        Log::info('Iniciando adição de usuários de teste...');

        $users = [
            [
                'name' => 'Douglas costa',
                'email' => 'douglas.costa@example.com',
                'perfil_acesso' => 1,
                'status' => 1,
            ],
            [
                'name' => 'Usuário Teste',
                'email' => 'test@example.com',
                'perfil_acesso' => 2,
                'status' => 1,
            ],
            [
                'name' => 'Usuário Inativo',
                'email' => 'inativo@example.com',
                'perfil_acesso' => 2,
                'status' => 0,
            ],
            [
                'name' => 'Douglas Silva',
                'email' => 'douglas2@example.com',
                'perfil_acesso' => 2,
                'status' => 1,
            ],
            [
                'name' => 'João Costa',
                'email' => 'costa@example.com',
                'perfil_acesso' => 2,
                'status' => 1,
            ],
        ];

        $password = Hash::make('password123');
        $inserted = [];
        $errors = [];

        foreach ($users as $userData) {
            try {
                $user = User::updateOrCreate(
                    ['email' => $userData['email']],
                    [
                        'name' => $userData['name'],
                        'password' => $password,
                        'perfil_acesso' => $userData['perfil_acesso'],
                        'status' => $userData['status'],
                    ]
                );

                $inserted[] = $user->name . ' (' . $user->email . ')';
                Log::info("Usuário adicionado: {$user->name}");
            } catch (\Exception $e) {
                $errors[] = $userData['email'] . ': ' . $e->getMessage();
                Log::error("Erro ao adicionar usuário: " . $e->getMessage());
            }
        }

        $count = User::count();

        $html = '
        <html>
            <head>
                <title>Adicionar Usuários de Teste</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 40px; }
                    .success { color: green; }
                    .error { color: red; }
                    ul { list-style-type: none; }
                    li { margin: 5px 0; }
                </style>
            </head>
            <body>
                <h1>✓ Usuários Adicionados com Sucesso!</h1>
                <h2>Usuários Inseridos:</h2>
                <ul class="success">';

        foreach ($inserted as $user) {
            $html .= '<li>✓ ' . $user . '</li>';
        }

        $html .= '</ul>';

        if (!empty($errors)) {
            $html .= '<h2>Erros:</h2><ul class="error">';
            foreach ($errors as $error) {
                $html .= '<li>✗ ' . $error . '</li>';
            }
            $html .= '</ul>';
        }

        $html .= '
                <h2>Status:</h2>
                <p><strong>Total de usuários no banco:</strong> ' . $count . '</p>
                <p><strong>Senha padrão:</strong> password123</p>
                <p><a href="/">← Voltar ao início</a></p>
                <p><a href="/usuarios">Ver usuários →</a></p>
            </body>
        </html>';

        return response($html);
    }

    /**
     * Verifica os usuários adicionados (APENAS TESTES)
     */
    public function checkUsers()
    {
        if (!env('APP_DEBUG')) {
            return response('Not allowed in production', 403);
        }

        $users = User::all();

        $html = '<html><head><title>Usuários no Banco</title></head><body>';
        $html .= '<h1>Usuários no Banco de Dados</h1>';
        $html .= '<p>Total: ' . $users->count() . '</p>';
        $html .= '<table border="1" cellpadding="10">';
        $html .= '<tr><th>ID</th><th>Nome</th><th>Email</th><th>Status</th><th>Perfil</th></tr>';

        foreach ($users as $user) {
            $status = $user->status ? 'Ativo' : 'Inativo';
            $html .= '<tr><td>' . $user->id . '</td><td>' . $user->name . '</td><td>' . $user->email . '</td><td>' . $status . '</td><td>' . $user->perfil_acesso . '</td></tr>';
        }

        $html .= '</table>';
        $html .= '<p><a href="/">← Voltar</a></p>';
        $html .= '</body></html>';

        return response($html);
    }

    /**
     * Testa a query de busca de usuários
     */
    public function debugSearch()
    {
        if (!env('APP_DEBUG')) {
            return response('Not allowed in production', 403);
        }

        $nome = request()->input('nome', '');
        $email = request()->input('email', '');
        $status = request()->input('status', '');

        $html = '<html><head><title>Debug da Busca</title><style>body{font-family:Arial;margin:20px;} pre{background:#f0f0f0;padding:10px;overflow:auto;} .error{color:red;} .success{color:green;}</style></head><body>';
        $html .= '<h1>Debug DETALHADO da Busca de Usuários</h1>';

        // Mostra os parametros brutos
        $html .= '<h2>1. Parâmetros RAW do Request:</h2>';
        $html .= '<pre>';
        $html .= 'REQUEST ARRAY: ' . json_encode(request()->all(), JSON_PRETTY_PRINT) . "\n";
        $html .= '</pre>';

        // Mostra os parametros processados
        $html .= '<h2>2. Parâmetros após input():</h2>';
        $html .= '<pre>';
        $html .= 'nome = "' . $nome . '" (length: ' . strlen($nome) . ', type: ' . gettype($nome) . ')' . "\n";
        $html .= 'email = "' . $email . '" (length: ' . strlen($email) . ', type: ' . gettype($email) . ')' . "\n";
        $html .= 'status = "' . $status . '" (length: ' . strlen($status) . ', type: ' . gettype($status) . ')' . "\n";
        $html .= "\n";
        $html .= 'empty($nome) = ' . var_export(empty($nome), true) . "\n";
        $html .= 'empty($email) = ' . var_export(empty($email), true) . "\n";
        $html .= 'empty($status) = ' . var_export(empty($status), true) . "\n";
        $html .= "\n";
        $html .= '$nome !== "" = ' . var_export($nome !== '', true) . "\n";
        $html .= '$email !== "" = ' . var_export($email !== '', true) . "\n";
        $html .= '$status !== "" = ' . var_export($status !== '', true) . "\n";
        $html .= '</pre>';

        // Constrói a query como o controlador faz
        $query = User::query();

        $html .= '<h2>3. Construção da Query:</h2>';
        $html .= '<pre>';

        if (!empty($nome)) {
            $query->where('name', 'like', "%{$nome}%");
            $html .= '<span class="success">✓ Aplicado: WHERE name LIKE "%' . $nome . '%"</span>' . "\n";
        } else {
            $html .= '<span class="error">✗ IGNORADO: nome está vazio</span>' . "\n";
        }

        if (!empty($email)) {
            $query->where('email', 'like', "%{$email}%");
            $html .= '<span class="success">✓ Aplicado: WHERE email LIKE "%' . $email . '%"</span>' . "\n";
        } else {
            $html .= '<span class="error">✗ IGNORADO: email está vazio</span>' . "\n";
        }

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
            $html .= '<span class="success">✓ Aplicado: WHERE status = ' . $status . '</span>' . "\n";
        } else {
            $html .= '<span class="error">✗ IGNORADO: status está vazio</span>' . "\n";
        }

        $html .= '</pre>';

        // Mostra a query SQL
        $html .= '<h2>4. SQL Executado:</h2>';
        $html .= '<pre>' . $query->toSql() . '</pre>';

        // Mostra as bindings
        $bindings = $query->getBindings();
        $html .= '<h2>5. Bindings:</h2>';
        $html .= '<pre>';
        foreach ($bindings as $key => $value) {
            $html .= "[$key] => " . var_export($value, true) . "\n";
        }
        $html .= '</pre>';

        // Executa e mostra o resultado
        $results = $query->get();
        $html .= '<h2 class="success">6. Resultados: ' . $results->count() . ' usuários encontrados</h2>';
        $html .= '<table border="1" cellpadding="10">';
        $html .= '<tr><th>ID</th><th>Nome</th><th>Email</th><th>Status</th></tr>';

        foreach ($results as $user) {
            $status_label = $user->status ? 'Ativo (1)' : 'Inativo (0)';
            $html .= '<tr><td>' . $user->id . '</td><td>' . $user->name . '</td><td>' . $user->email . '</td><td>' . $status_label . '</td></tr>';
        }

        $html .= '</table>';

        $html .= '<hr/><p><a href="/">← Voltar</a></p>';
        $html .= '</body></html>';

        return response($html);
    }
}
