<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Portal do Cliente
|--------------------------------------------------------------------------
|
| Área exclusiva do cliente, autenticada pelo guard "cliente" (login por
| CPF + senha, ver App\Http\Controllers\Portal\AuthController). Totalmente
| separada das rotas administrativas em routes/web.php: nenhuma delas deve
| ser reaproveitada aqui, para nunca expor dados de outros clientes.
|
*/

Route::prefix('portal')->name('portal.')->group(function () {
    Route::get('/login', [App\Http\Controllers\Portal\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [App\Http\Controllers\Portal\AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [App\Http\Controllers\Portal\AuthController::class, 'logout'])->name('logout');

    Route::middleware('auth:cliente')->group(function () {
        Route::get('/', [App\Http\Controllers\Portal\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/processos', [App\Http\Controllers\Portal\ProcessosController::class, 'index'])->name('processos');
        Route::get('/processos/{id}', [App\Http\Controllers\Portal\ProcessosController::class, 'show'])->name('processos.show');
        Route::get('/documentos', [App\Http\Controllers\Portal\DocumentosController::class, 'index'])->name('documentos');
        Route::get('/documentos/{id}/preview', [App\Http\Controllers\Portal\DocumentosController::class, 'preview'])->name('documentos.preview');
        Route::get('/financeiro', [App\Http\Controllers\Portal\FinanceiroController::class, 'index'])->name('financeiro');
        Route::get('/agenda', [App\Http\Controllers\Portal\AgendaController::class, 'index'])->name('agenda');
    });
});
