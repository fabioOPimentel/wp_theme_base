<?php

namespace App;

require_once __DIR__ . '../../security.php';

class EnfileirandoEstilos
{

    public function __construct()
    {

    }

    public function enfileirandoEstilo()
    {
        //Css
        wp_enqueue_style('bootstrap', get_template_directory_uri() . '/node_modules/bootstrap3/dist/css/bootstrap.min.css', array(), '');
        wp_enqueue_style('font-awesome', get_template_directory_uri() . '/node_modules/font-awesome/css/font-awesome.min.css', array(), '');
        wp_enqueue_style('slick', get_template_directory_uri() . '/node_modules/slick-carousel/slick/slick.css', array(), '');
        wp_enqueue_style('slick-theme', get_template_directory_uri() . '/node_modules/slick-carousel/slick/slick-theme.css', array(), '');
        wp_enqueue_style('template', get_template_directory_uri() . '/assets/css/template.css', array(), '');
        //Scripts
        wp_enqueue_script('jquery-nao-nativo', get_template_directory_uri() . '/node_modules/jquery/dist/jquery.js', array(), '', true);
        wp_enqueue_script('bootstrap', get_template_directory_uri() . '/node_modules/bootstrap3/dist/js/bootstrap.js', array(), '', true);
        wp_enqueue_script('vue', get_template_directory_uri() . '/node_modules/vue/dist/vue.js', array(), '', true);
        wp_enqueue_script('vue-resource', get_template_directory_uri() . '/node_modules/vue-resource/dist/vue-resource.js', array(), '', true);
        wp_enqueue_script('slick', get_template_directory_uri() . '/node_modules/slick-carousel/slick/slick.js', array(), '', true);
        wp_enqueue_script('template-js', get_template_directory_uri() . '/assets/js/template.js', array(), '', true);
    }
    
    private function expecificandoPagina($page,$style,$nome,$endereco,$versao)
    {
        if(is_page($page)):
            if($style == true):
                wp_enqueue_style($nome, get_template_directory_uri() . $endereco, array(), $versao);
            else:
                wp_enqueue_script($nome, get_template_directory_uri() . $endereco, array(), $versao, true);
            endif;
        endif;
    }

}
