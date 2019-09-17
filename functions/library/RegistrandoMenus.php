<?php

namespace Library;

if ( !defined( 'ABSPATH' ) ) { exit; };

class RegistrandoMenus
{

    public function registrandoMenu()
    {
        register_nav_menus([
            'menu_principal' => 'Menu Principal',
            'sub_menu_footer' => 'Sub Menu Footer'
        ]);
    }

}
