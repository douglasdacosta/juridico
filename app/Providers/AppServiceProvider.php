<?php

namespace App\Providers;

use App\Models\CategoriaMenus;
use App\Models\Menus;
use App\Models\SubMenus;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use App\Http\Controllers\Auth\ValidaPermissaoAcessoController;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

 /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {

        $events->Listen(BuildingMenu::class, function (BuildingMenu $event) {

            $categorias = app(CategoriaMenus::class)::where('id', '>', '0')->get()->sortBy('id')->map(function (CategoriaMenus $categoriaMenus) {
                return [
                    'key' => 'categoria_'.$categoriaMenus->id,
                    'header' => $categoriaMenus->nome
                ];
            });

            $event->menu->add(...$categorias);

            $menus = app(Menus::class)::where('id', '>', '0')->get()->sortByDesc('id')->map(function (Menus $menus) use(&$event) {
                    return $event->menu->addafter('categoria_'.$menus->categoria_menu_id,
                        [
                            'key' => 'menu_'.$menus->id,
                            'text' => $menus->nome,
                            'icon' => $menus->icon,
                            'icon_color' => 'cyan',
                            'expanded' => true
                        ]
                );
            });


            $perfis_menu = (new ValidaPermissaoAcessoController())->GetSubMenuLiberado();

            if(!is_array($perfis_menu)) {
                $perfis_menu = [];
            }

            $submenus = app(SubMenus::class)::where('id', '>', '0')->get()->sortBy('id')->map(function (SubMenus $submenus) use(&$event, $perfis_menu) {

                if(!in_array($submenus->id, $perfis_menu)) {
                    return [];
                }

                // Check if this is the current route
                $isActive = request()->route()?->getName() === $submenus->rota;

                return $event->menu->addin('menu_'.$submenus->menu_id,
                    [
                        'key' => 'submenu_'.$submenus->id,
                        'text' => $submenus->nome,
                        'url'  => $submenus->rota,
                        'icon' => $submenus->icon,
                        'icon_color' => $submenus->icon_color,
                        'active' => $isActive,
                        //'label' => '',
                    ]
                );
            });

        });

    }
}
