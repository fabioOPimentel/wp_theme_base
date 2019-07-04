<?php

namespace Library;

require_once __DIR__ . '../../security.php';

class EnfileirandoEstilos
{
    public function enfileirandoEstilo()
    {
        //Css
        wp_enqueue_style('bootstrap','https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css', array(), '');
        wp_enqueue_style('font-awesome', get_template_directory_uri() . '/node_modules/font-awesome/css/font-awesome.min.css', array(), '');
        wp_enqueue_style('slick', get_template_directory_uri() . '/node_modules/slick-carousel/slick/slick.css', array(), '');
        wp_enqueue_style('slick-theme', get_template_directory_uri() . '/node_modules/slick-carousel/slick/slick-theme.css', array(), '');
        wp_enqueue_style('Montserrat', "https://fonts.googleapis.com/css?family=Montserrat:400,500,700,900&display=swap", array(), '');
        wp_enqueue_style('template', get_template_directory_uri() . '/assets/css/template.css', array(), '');
        //Scripts
        wp_enqueue_script('Polyfill', get_template_directory_uri() . '/node_modules/intersection-observer/intersection-observer.js', array(), null, true);
        wp_enqueue_script('jquery-nao-nativo', get_template_directory_uri() . '/node_modules/jquery/dist/jquery.js', array(), '', true);
        wp_enqueue_script('popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', array(), '', true);
        wp_enqueue_script('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array(), '', true);
        wp_enqueue_script('vue', get_template_directory_uri() . '/node_modules/vue/dist/vue.js', array(), '', true);
        wp_enqueue_script('vue-resource', get_template_directory_uri() . '/node_modules/vue-resource/dist/vue-resource.js', array(), '', true);
        wp_enqueue_script('lozad', get_template_directory_uri() . '/node_modules/lozad/dist/lozad.js', array(), '', true);
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
