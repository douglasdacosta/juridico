<?php

use App\Http\Controllers\AndamentosController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\FiliaisController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API para Select2 - MOVIDAS PARA routes/web.php para garantir sessão com middleware auth
// Route::middleware('web')->get('/clientes/search', [ClientesController::class, 'apiSearch'])->name('api.clientes.search');
// Route::middleware('web')->get('/filiais/search', [FiliaisController::class, 'apiSearch'])->name('api.filiais.search');

// Rota de andamentos movida para routes/web.php para ter middleware 'web' com sessão
