<?php

namespace Library;

if ( !defined( 'ABSPATH' ) ) { exit; };

class EnfileirandoEstilos
{
    public function enfileirandoEstilo()
    {
        //Css
        wp_enqueue_style('template', get_template_directory_uri() . '/dist/css/main.css', array(), null);
        //Scripts
        //wp_enqueue_script('vendor', ROOT.'/dist/js/vendors.main.22452cb13429dd96fe8c.js', array(), null, true);
        //wp_enqueue_script('main', ROOT.'/dist/js/main.22452cb13429dd96fe8c.js', array('vendor'), null, true);
        wp_enqueue_script('main', ROOT.'/dist/js/main.js', array(), null, true);
        $this->expecificandoPagina('page-with-captha',false,'recaptcha-API','https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit',null);
    }
    
    private function expecificandoPagina($page,$style,$nome,$endereco,$versao)
    {
        if(is_page($page)):
            if($style == true):
                wp_enqueue_style($nome, $endereco, array(), $versao);
            else:
                wp_enqueue_script($nome, $endereco, array(), $versao, true);
            endif;
        endif;
    }

    private function modifyJsxTag($myHandler)
    {
        add_filter( 'script_loader_tag', function($tag, $handle, $src) use ($myHandler){
            if ( $myHandler == $handle ) {
                $tag = str_replace( "<script type='text/javascript'", "<script type='module'", $tag );
            }
            return $tag;
        }, 10, 3 );
    }

}
