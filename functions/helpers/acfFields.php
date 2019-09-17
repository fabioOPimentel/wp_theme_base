<?php

namespace Helpers;

if ( !defined( 'ABSPATH' ) ) { exit; };

use StoutLogic\AcfBuilder\FieldsBuilder;

class acfFields {
    /**
     * Singleton
     */
    
    private static $instance;

    public static function getInstance() 
    {
        if (self::$instance == NULL):
            self::$instance = new self();
        endif;
    }

    public function __construct()
    {
        $this->customField();
        $this->fieldContato();
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

    public function fieldContato(){
        $banner = new FieldsBuilder('banner');
        $banner
            ->addText('title')
            ->addWysiwyg('content')
            ->addImage('background_image')
            ->addRepeater('slides')
            ->addWysiwyg('content')
            ->setLocation('post_type', '==', 'contato');

        add_action('acf/init', function() use ($banner) {
            acf_add_local_field_group($banner->build());
        });
    }
}