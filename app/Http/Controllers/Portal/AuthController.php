<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * Autenticação do Portal do Cliente (guard "cliente"): login por CPF + senha
 * definida por um usuário interno em ClientesController::definirAcessoPortal.
 * Não há cadastro/recuperação de senha self-service nesta versão — sem canal
 * de e-mail/SMS configurado no projeto, a mesma limitação já registrada para
 * a cobrança automática via WhatsApp (Fase 2.4 do plano de ação).
 */
class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('cliente')->check()) {
            return redirect()->route('portal.dashboard');
        }

        return view('portal.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'cpf' => 'required|string',
            'password' => 'required|string',
        ], [
            'cpf.required' => 'Informe o CPF.',
            'password.required' => 'Informe a senha.',
        ]);

        $throttleKey = 'portal-login:' . $request->ip() . ':' . preg_replace('/\D/', '', $validated['cpf']);

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'cpf' => ["Muitas tentativas. Tente novamente em " . ceil($seconds / 60) . " minuto(s)."],
            ]);
        }

        $cpfDigits = preg_replace('/\D/', '', $validated['cpf']);

        $cliente = Cliente::query()
            ->whereRaw("REPLACE(REPLACE(REPLACE(cpf, '.', ''), '-', ''), ' ', '') = ?", [$cpfDigits])
            ->first();

        if (! $cliente || ! $cliente->password || ! Hash::check($validated['password'], $cliente->password)) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'cpf' => ['CPF ou senha inválidos.'],
            ]);
        }

        if (! $cliente->ativo) {
            throw ValidationException::withMessages([
                'cpf' => ['Cadastro inativo. Contate o escritório.'],
            ]);
        }

        RateLimiter::clear($throttleKey);

        $request->session()->regenerate();
        Auth::guard('cliente')->login($cliente);

        return redirect()->route('portal.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('cliente')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal.login');
    }
}
