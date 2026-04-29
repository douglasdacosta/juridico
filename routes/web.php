<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

// Rota temporária para adicionar usuários de teste (APENAS DESENVOLVIMENTO)
Route::get('/add-test-users', [App\Http\Controllers\TestDataController::class, 'addTestUsers']);
Route::get('/check-users', [App\Http\Controllers\TestDataController::class, 'checkUsers']);
Route::get('/debug-search', [App\Http\Controllers\TestDataController::class, 'debugSearch']);

Auth::routes(['register' => False, 'reset' => false]);

Route::match(['get', 'post'],'/perfis', [App\Http\Controllers\PerfisController::class, 'index'])->name('perfis')->middleware('auth', 'afterAuth:perfis');
Route::match(['get', 'post'],'/alterar-perfis', [App\Http\Controllers\PerfisController::class, 'alterar'])->name('alterar-perfis')->middleware('auth', 'afterAuth:perfis');
Route::match(['get', 'post'],'/incluir-perfis', [App\Http\Controllers\PerfisController::class, 'incluir'])->name('incluir-perfis')->middleware('auth', 'afterAuth:perfis');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::match(['get', 'post'], '/clientes', [App\Http\Controllers\ClientesController::class, 'index'])->name('clientes')->middleware('auth', 'afterAuth:clientes');
Route::match(['get', 'post'], '/alterar-clientes', [App\Http\Controllers\ClientesController::class, 'alterar'])->name('alterar-clientes')->middleware('auth', 'afterAuth:clientes');
Route::match(['get', 'post'], '/incluir-clientes', [App\Http\Controllers\ClientesController::class, 'incluir'])->name('incluir-clientes')->middleware('auth', 'afterAuth:clientes');
Route::post('/desativar-clientes', [App\Http\Controllers\ClientesController::class, 'desativar'])->name('desativar-clientes')->middleware('auth', 'afterAuth:clientes');
Route::get('/exportar-clientes-csv', [App\Http\Controllers\ClientesController::class, 'exportCsv'])->name('exportar-clientes-csv')->middleware('auth');
Route::get('/exportar-clientes-pdf', [App\Http\Controllers\ClientesController::class, 'exportPrint'])->name('exportar-clientes-pdf')->middleware('auth');

Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])
    ->name('settings')
    ->middleware('auth');

Route::post('/settings', [App\Http\Controllers\SettingsController::class, 'edit'])
    ->name('settings.update')
    ->middleware('auth');

Route::match(['get', 'post'], '/filiais', [App\Http\Controllers\FiliaisController::class, 'index'])
    ->name('filiais')
    ->middleware('auth');

Route::match(['get', 'post'], '/alterar-filiais', [App\Http\Controllers\FiliaisController::class, 'alterar'])
    ->name('alterar-filiais')
    ->middleware('auth');

Route::match(['get', 'post'], '/incluir-filiais', [App\Http\Controllers\FiliaisController::class, 'incluir'])
    ->name('incluir-filiais')
    ->middleware('auth');

Route::match(['get', 'post'], '/processos', [App\Http\Controllers\ProcessosController::class, 'index'])
    ->name('processos')
    ->middleware('auth');

Route::match(['get', 'post'], '/alterar-processos', [App\Http\Controllers\ProcessosController::class, 'alterar'])
    ->name('alterar-processos')
    ->middleware('auth');

Route::match(['get', 'post'], '/incluir-processos', [App\Http\Controllers\ProcessosController::class, 'incluir'])
    ->name('incluir-processos')
    ->middleware('auth');
Route::match(['get', 'post'], '/incluir-processo', [App\Http\Controllers\ProcessosController::class, 'incluir'])
    ->name('incluir-processo')
    ->middleware('auth');
Route::get('/exportar-processos-csv', [App\Http\Controllers\ProcessosController::class, 'exportCsv'])
    ->name('exportar-processos-csv')
    ->middleware('auth');
Route::get('/exportar-processos-pdf', [App\Http\Controllers\ProcessosController::class, 'exportPrint'])
    ->name('exportar-processos-pdf')
    ->middleware('auth');

// Rota de listagem de andamentos removida - andamentos são gerenciados dentro dos processos
// Route::match(['get', 'post'], '/andamentos', [App\Http\Controllers\AndamentosController::class, 'index'])
//     ->name('andamentos')
//     ->middleware('auth');

// Rotas AJAX mantidas para funcionalidades dentro dos processos
Route::match(['get', 'post', 'delete'], '/alterar-andamentos', [App\Http\Controllers\AndamentosController::class, 'alterar'])
    ->name('alterar-andamentos')
    ->middleware('auth');

Route::match(['get', 'post'], '/incluir-andamentos', [App\Http\Controllers\AndamentosController::class, 'incluir'])
    ->name('incluir-andamentos')
    ->middleware('auth');
Route::match(['get', 'post'], '/incluir-andamento', [App\Http\Controllers\AndamentosController::class, 'incluir'])
    ->name('incluir-andamento')
    ->middleware('auth');

// Tipos de Ação
Route::match(['get', 'post'], '/tipos-acao', [App\Http\Controllers\TiposAcaoController::class, 'index'])
    ->name('tipos-acao')
    ->middleware('auth');

Route::match(['get', 'post'], '/incluir-tipos-acao', [App\Http\Controllers\TiposAcaoController::class, 'incluir'])
    ->name('incluir-tipos-acao')
    ->middleware('auth');

Route::match(['get', 'post'], '/alterar-tipos-acao', [App\Http\Controllers\TiposAcaoController::class, 'alterar'])
    ->name('alterar-tipos-acao')
    ->middleware('auth');

Route::post('/desativar-tipos-acao', [App\Http\Controllers\TiposAcaoController::class, 'desativar'])
    ->name('desativar-tipos-acao')
    ->middleware('auth');

Route::match(['get', 'post'], '/documentos', [App\Http\Controllers\DocumentosController::class, 'index'])
    ->name('documentos')
    ->middleware('auth');

Route::match(['get', 'post'], '/incluir-documentos', [App\Http\Controllers\DocumentosController::class, 'incluir'])
    ->name('incluir-documentos')
    ->middleware('auth');

Route::match(['get', 'post'], '/alterar-documentos', [App\Http\Controllers\DocumentosController::class, 'alterar'])
    ->name('alterar-documentos')
    ->middleware('auth');

Route::post('/desativar-documentos', [App\Http\Controllers\DocumentosController::class, 'desativar'])
    ->name('desativar-documentos')
    ->middleware('auth');

Route::post('/excluir-documentos', [App\Http\Controllers\DocumentosController::class, 'excluir'])
    ->name('excluir-documentos')
    ->middleware('auth');

Route::get('/preview-documentos/{id}', [App\Http\Controllers\DocumentosController::class, 'preview'])
    ->name('preview-documentos')
    ->middleware('auth');

// Tipos de Ação CRUD
Route::match(['get', 'post'], '/tipos-acao', [App\Http\Controllers\TipoAcaoController::class, 'index'])
    ->name('tipos-acao')
    ->middleware('auth');

Route::match(['get', 'post'], '/incluir-tipos-acao', [App\Http\Controllers\TipoAcaoController::class, 'incluir'])
    ->name('incluir-tipos-acao')
    ->middleware('auth');

Route::match(['get', 'post'], '/alterar-tipos-acao', [App\Http\Controllers\TipoAcaoController::class, 'alterar'])
    ->name('alterar-tipos-acao')
    ->middleware('auth');

Route::post('/desativar-tipos-acao', [App\Http\Controllers\TipoAcaoController::class, 'desativar'])
    ->name('desativar-tipos-acao')
    ->middleware('auth');

Route::post('/excluir-tipos-acao', [App\Http\Controllers\TipoAcaoController::class, 'excluir'])
    ->name('excluir-tipos-acao')
    ->middleware('auth');

// Usuários CRUD
Route::get('/usuarios', [App\Http\Controllers\UsuariosController::class, 'index'])
    ->name('usuarios')
    ->middleware('auth');

Route::post('/usuarios', [App\Http\Controllers\UsuariosController::class, 'index'])
    ->name('usuarios.post')
    ->middleware('auth');

Route::match(['get', 'post'], '/incluir-usuarios', [App\Http\Controllers\UsuariosController::class, 'incluir'])
    ->name('incluir-usuarios')
    ->middleware('auth');

Route::match(['get', 'post'], '/alterar-usuarios', [App\Http\Controllers\UsuariosController::class, 'alterar'])
    ->name('alterar-usuarios')
    ->middleware('auth');

Route::post('/desativar-usuarios', [App\Http\Controllers\UsuariosController::class, 'desativar'])
    ->name('desativar-usuarios')
    ->middleware('auth');

Route::post('/ativar-usuarios', [App\Http\Controllers\UsuariosController::class, 'ativar'])
    ->name('ativar-usuarios')
    ->middleware('auth');

Route::post('/excluir-usuarios', [App\Http\Controllers\UsuariosController::class, 'excluir'])
    ->name('excluir-usuarios')
    ->middleware('auth');

Route::post('/resetar-senha-usuarios', [App\Http\Controllers\UsuariosController::class, 'resetarSenha'])
    ->name('resetar-senha-usuarios')
    ->middleware('auth');

// API Routes com sessão (movido de routes/api.php para ter middleware 'web' com sessão)
Route::get('/api/andamentos/por-processo/{processoId}', [App\Http\Controllers\AndamentosController::class, 'porProcesso'])->name('api.andamentos.por-processo')->middleware('auth');
Route::get('/api/andamentos/{id}', [App\Http\Controllers\AndamentosController::class, 'show'])->name('api.andamentos.show')->middleware('auth');
Route::get('/api/clientes/search', [App\Http\Controllers\ClientesController::class, 'apiSearch'])->name('api.clientes.search')->middleware('auth');
Route::get('/api/filiais/search', [App\Http\Controllers\FiliaisController::class, 'apiSearch'])->name('api.filiais.search')->middleware('auth');
Route::get('/api/tipos-acao/search', [App\Http\Controllers\TipoAcaoController::class, 'apiSearch'])->name('api.tipos-acao.search')->middleware('auth');
Route::get('/api/usuarios/search', [App\Http\Controllers\UsuariosController::class, 'apiSearch'])->name('api.usuarios.search')->middleware('auth');
