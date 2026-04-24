<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\ValidaPermissaoAcessoController;
use App\Models\Acoes;
use App\Models\PerfilSubmenus;
use App\Models\Perfis;
use App\Models\PermissoesPerfis;
use App\Models\SubMenus;
use Illuminate\Http\Request;

class PerfisController extends Controller
{
    public $permissoes_liberadas = [];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {

        $this->permissoes_liberadas = (new ValidaPermissaoAcessoController())->validaAcaoLiberada(1, (new ValidaPermissaoAcessoController())->retornaPerfil());
        $perfil = (new ValidaPermissaoAcessoController())->retornaPerfil();
        if($perfil != 1) {
                return redirect()->route('perfis');
        }
        $perfis = new Perfis();

        $id = !empty($request->input('id')) ? ($request->input('id')) : ( !empty($id) ? $id : false );

        if ($id) {
            $perfis = $perfis->where('id', '=', $id);
        }

        if ($request->input('nome') != '') {
        	$perfis = $perfis->where('nome', 'like', '%'.$request->input('nome').'%');
        }


        $acoes =new Acoes();
        $acoes = $acoes->get();

        $perfis = $perfis->get();
        $tela = 'pesquisa';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'perfis',
                'acoes' => $acoes,
                'permissoes_liberadas' => $this->permissoes_liberadas,
				'perfis'=> $perfis,
				'request' => $request,
				'rotaIncluir' => 'incluir-perfis',
				'rotaAlterar' => 'alterar-perfis'
			);

        return view('perfis', $data);
    }

     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function incluir(Request $request)
    {
        $perfil = (new ValidaPermissaoAcessoController())->retornaPerfil();
        if($perfil != 1) {
                return redirect()->route('perfis');
        }
        $metodo = $request->method();

    	if ($metodo == 'POST') {

    		$perfis_id = $this->salva($request);

	    	return redirect()->route('perfis', [ 'id' => $perfis_id ] );

    	}


        $telas = new SubMenus();
        $telas = $telas->where('ativo', '=', 1)->get();

        $acoes =new Acoes();
        $acoes = $acoes->get();

        $tela = 'incluir';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'perfis',
                'telas' => $telas,
                'acoes' => $acoes,
                'permissoes_liberadas' => $this->permissoes_liberadas,
				'request' => $request,
				'rotaIncluir' => 'incluir-perfis',
				'rotaAlterar' => 'alterar-perfis'
			);




        return view('perfis', $data);
    }

     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function alterar(Request $request)
    {

        $perfil = (new ValidaPermissaoAcessoController())->retornaPerfil();
        if($perfil != 1) {
                return redirect()->route('perfis');
        }
        $perfis = new Perfis();


        $perfis= $perfis->where('id', '=', $request->input('id'))->get();

		$metodo = $request->method();
		if ($metodo == 'POST') {

    		$perfis_id = $this->salva($request);

	    	return redirect()->route('perfis', [ 'id' => $perfis_id ] );

    	}

        $telas = new SubMenus();
        $telas = $telas->where('ativo', '=', 1)->get();

        $PerfilSubmenus = new PerfilSubmenus();
        $PerfilSubmenus = $PerfilSubmenus->where('perfil_id', '=', $request->input('id'))->get()->toArray();

        $array_PermissoesPerfis = array();
        foreach ($telas as $key => $value) {

            $value->checked = false;
            foreach ($PerfilSubmenus as $key => $value2) {
                if ($value->id == $value2['submenu_id']) {
                    $value->checked = true;
                }
            }

            $PermissoesPerfis = new PermissoesPerfis();


            $permissoes = $PermissoesPerfis->where('perfil_id', '=', $request->input('id'))->where('submenus_id', '=', $value->id)->get()->toArray();

            foreach ($permissoes as $key => $permissao) {
                $array_PermissoesPerfis[$request->input('id')][$value->id]['acoes'][] = $permissao['acao_id'];
            }

        }

        $acoes =new Acoes();
        $acoes = $acoes->get();

        $tela = 'alterar';
    	$data = array(
				'tela' => $tela,
                'nome_tela' => 'perfis',
                'telas' => $telas,
                'acoes' => $acoes,
                'permissoes' => $array_PermissoesPerfis,
				'perfis'=> $perfis,
				'request' => $request,
				'rotaIncluir' => 'incluir-perfis',
				'rotaAlterar' => 'alterar-perfis'
			);

        return view('perfis', $data);
    }

    public function salva($request) {
        $perfis = new Perfis();

        if($request->input('id')) {
            $perfis = $perfis::find($request->input('id'));
        }

        $perfis->nome = $request->input('nome');
        $permissoes = $request->input('permissoes');

        $perfis->save();

        if(!empty($request->input('telas'))) {

            $array_telas = $request->input('telas');

            $subMenus = SubMenus::whereIn('id', $array_telas)->get();

            $perfis->subMenus()->sync($subMenus->pluck('id'));
        }else {

            $perfis->subMenus()->sync([]);
        }

        PermissoesPerfis::where('perfil_id', $perfis->id)->delete();

        foreach ($permissoes as $key => $permissao) {
            list($tela, $acao) = explode("_", $permissao);

            PermissoesPerfis::updateOrCreate(
                [
                    'perfil_id' => $perfis->id,
                    'acao_id' => $acao,
                    'submenus_id' => $tela
                ],
                []
            );
        }



        return $perfis->id;

    }
}
