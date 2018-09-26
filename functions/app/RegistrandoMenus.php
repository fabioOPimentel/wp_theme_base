<?php

namespace App;

require_once __DIR__ . '../../security.php';

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
