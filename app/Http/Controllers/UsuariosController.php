<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Perfis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuariosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tela = $request->input('tela', 'pesquisa');
        $nome = $request->input('nome', '');
        $email = $request->input('email', '');
        $status = $request->input('status', '');

        \Log::info('Busca de usuários', [
            'nome' => $nome,
            'email' => $email,
            'status' => $status,
            'tela' => $tela,
        ]);

        $usuarios = User::query()->with('perfil');

        if (!empty($nome)) {
            \Log::info('Filtrando por nome: ' . $nome);
            $usuarios->where('name', 'like', "%{$nome}%");
        }

        if (!empty($email)) {
            \Log::info('Filtrando por email: ' . $email);
            $usuarios->where('email', 'like', "%{$email}%");
        }

        // FIX: Verificar se status não é null E não é vazio
        if ($status !== null && $status !== '') {
            \Log::info('Filtrando por status: ' . $status);
            $usuarios->where('status', $status);
        }

        $usuarios = $usuarios->paginate(15);

        \Log::info('Total de usuários encontrados: ' . $usuarios->total());

        $perfis = Perfis::all();

        $data = [
            'tela' => $tela,
            'nome_tela' => 'usuarios',
            'usuarios' => $usuarios,
            'perfis' => $perfis,
            'nome' => $nome,
            'email' => $email,
            'status' => $status,
        ];

        return view('usuarios', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function incluir(Request $request)
    {
        $metodo = $request->method();

        if ($metodo == 'POST') {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'perfil_acesso' => 'required|exists:perfis,id',
                'status' => 'required|in:1,0',
            ]);

            $usuario = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'perfil_acesso' => $request->input('perfil_acesso'),
                'status' => $request->input('status'),
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Usuário criado com sucesso.',
                    '_token' => csrf_token(),
                ]);
            }

            return redirect()->route('usuarios')->with('success', 'Usuário criado com sucesso.');
        }

        $perfis = Perfis::all();
        $tela = 'incluir';

        $data = [
            'tela' => $tela,
            'nome_tela' => 'usuarios',
            'perfis' => $perfis,
        ];

        return view('usuarios', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function alterar(Request $request)
    {
        $metodo = $request->method();
        $id = $request->input('id');

        if (!$id) {
            return redirect()->route('usuarios')->with('error', 'Usuário não encontrado.');
        }

        $usuario = User::findOrFail($id);

        if ($metodo == 'POST') {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'perfil_acesso' => 'required|exists:perfis,id',
                'status' => 'required|in:1,0',
            ]);

            $usuario->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'perfil_acesso' => $request->input('perfil_acesso'),
                'status' => $request->input('status'),
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Usuário alterado com sucesso.',
                    '_token' => csrf_token(),
                ]);
            }

            return redirect()->route('usuarios')->with('success', 'Usuário alterado com sucesso.');
        }

        $perfis = Perfis::all();
        $tela = 'alterar';

        $data = [
            'tela' => $tela,
            'nome_tela' => 'usuarios',
            'usuario' => $usuario,
            'perfis' => $perfis,
        ];

        return view('usuarios', $data);
    }

    /**
     * Change password for a user.
     */
    public function resetarSenha(Request $request)
    {
        $id = $request->input('id');
        $usuario = User::findOrFail($id);

        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $usuario->update([
            'password' => Hash::make($request->input('password')),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Senha alterada com sucesso.',
                '_token' => csrf_token(),
            ]);
        }

        return redirect()->route('usuarios')->with('success', 'Senha alterada com sucesso.');
    }

    /**
     * Soft delete (deactivate) the specified resource.
     */
    public function desativar(Request $request)
    {
        $id = $request->input('id');
        $usuario = User::findOrFail($id);

        $usuario->update(['status' => 0]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Usuário desativado com sucesso.',
                '_token' => csrf_token(),
            ]);
        }

        return redirect()->route('usuarios')->with('success', 'Usuário desativado com sucesso.');
    }

    /**
     * Restore (reactivate) the specified resource.
     */
    public function ativar(Request $request)
    {
        $id = $request->input('id');
        $usuario = User::findOrFail($id);

        $usuario->update(['status' => 1]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Usuário ativado com sucesso.',
                '_token' => csrf_token(),
            ]);
        }

        return redirect()->route('usuarios')->with('success', 'Usuário ativado com sucesso.');
    }

    /**
     * Delete the specified resource from storage.
     */
    public function excluir(Request $request)
    {
        $id = $request->input('id');
        $usuario = User::findOrFail($id);

        // Prevent deleting the current user
        if ($usuario->id === auth()->id()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não pode deletar sua própria conta.',
                    '_token' => csrf_token(),
                ], 403);
            }
            return redirect()->route('usuarios')->with('error', 'Você não pode deletar sua própria conta.');
        }

        $usuario->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Usuário deletado com sucesso.',
                '_token' => csrf_token(),
            ]);
        }

        return redirect()->route('usuarios')->with('success', 'Usuário deletado com sucesso.');
    }

    /**
     * API search for Select2
     */
    public function apiSearch(Request $request)
    {
        $search = $request->input('q', '');
        $page = $request->input('page', 1);
        $perPage = 10;

        $usuarios = User::where('status', 1)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->paginate($perPage, ['id', 'name', 'email'], 'page', $page);

        return response()->json([
            'results' => $usuarios->map(function ($usuario) {
                return [
                    'id' => $usuario->id,
                    'text' => $usuario->name . ' (' . $usuario->email . ')',
                ];
            })->values(),
            'pagination' => [
                'more' => $usuarios->hasMorePages(),
            ],
        ]);
    }
}
