<?php

namespace Helpers;

require_once __DIR__ . '../../security.php';

use StoutLogic\AcfBuilder\FieldsBuilder;

class acfFields {

    public function __construct() 
    {
        $this->customField();
    }

    public function customField()
    {
        $banner = new FieldsBuilder('banners-header', array('title'=>'Imagem de destaque para o cabeçalho'));
        $banner
            ->addImage('background_image')
            ->setLocation('post_type', '==', 'page');

        add_action('acf/init', function() use ($banner) {
        acf_add_local_field_group($banner->build());
        });
    }
}