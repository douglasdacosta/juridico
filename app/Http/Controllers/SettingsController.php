<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreSettingsRequest;
use App\Http\Requests\UpdateSettingsRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id = Auth::user()->id;
        $user = User::find($id);

        $this->authorize('view', $user);

        $data = array(
            'user' => $user,
        );
        return view('settings', $data);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSettingsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSettingsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function show($settings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $User
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
            $id = Auth::user()->id;
    		$user = new User();
    		$user = User::find($id);

		    $this->authorize('update', $user);

		    $request->validate([
		        'nome' => 'required|string|max:200',
		        'email' => 'required|email',
		        'password' => 'nullable|string|min:8',
		        'status' => 'nullable|in:A,I',
                'lgpd_purpose' => 'nullable|string|max:1000',
                'lgpd_consent' => 'nullable|boolean',
            ], [
                'nome.required' => 'O nome é obrigatório.',
                'email.required' => 'O e-mail é obrigatório.',
                'email.email' => 'Informe um e-mail válido.',
                'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
                'status.in' => 'Status inválido.',
                'lgpd_purpose.max' => 'A finalidade LGPD deve ter no máximo 1000 caracteres.',
		    ]);

    		$user->name = $request->input('nome');

		    if ($request->filled('numero')) {
                $user->numero = $request->input('numero');
            }
		    if ($request->filled('complemento')) {
                $user->complemento = $request->input('complemento');
            }
		    if ($request->filled('telefone')) {
                $user->telefone = preg_replace("/[^0-9]/", "", $request->input('telefone'));
            }
		    if ($request->filled('cep')) {
                $user->cep = $request->input('cep');
            }
		    if ($request->filled('endereco')) {
                $user->endereco = $request->input('endereco');
            }
		    if ($request->filled('bairro')) {
                $user->bairro = $request->input('bairro');
            }
		    if ($request->filled('cidade')) {
                $user->cidade = $request->input('cidade');
            }
		    if ($request->filled('estado')) {
                $user->estado = $request->input('estado');
            }

		    if ($request->filled('status')) {
                $user->status = $request->input('status');
            }

    		$user->email = $request->input('email');

            if(!empty(trim($request->input('password')))) {
                $user->password = Hash::make(trim($request->input('password')));
            }

		    $enableTwoFactor = $request->boolean('two_factor_enabled');
		    if ($enableTwoFactor) {
		        $user->two_factor_enabled = true;
		        if (empty($user->two_factor_secret)) {
		            $user->two_factor_secret = Str::random(32);
		        }
		    } else {
		        $user->two_factor_enabled = false;
		        $user->two_factor_secret = null;
		    }

            $lgpdConsent = $request->boolean('lgpd_consent');
            $lgpdPurpose = trim((string) $request->input('lgpd_purpose', ''));

            if ($lgpdConsent && $lgpdPurpose === '') {
                return redirect()->back()
                    ->withErrors(['lgpd_purpose' => 'Informe a finalidade do tratamento de dados ao conceder consentimento LGPD.'])
                    ->withInput();
            }

            $user->lgpd_consent_at = $lgpdConsent ? now() : null;
            $user->lgpd_purpose = $lgpdConsent ? $lgpdPurpose : null;

    		$user->save();

        return redirect()->route('settings')->with('success', 'Configurações atualizadas com sucesso.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSettingsRequest  $request
     * @param  \App\Models\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSettingsRequest $request, $settings)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function destroy($settings)
    {
        //
    }
}
