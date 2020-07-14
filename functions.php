<?php

if ( !defined( 'ABSPATH' ) ) { exit; };

//Carregando autoloader do composer
require 'vendor/autoload.php';

//Carregando helpers
use Helpers\acfFields;
new Helpers\acfMissing;
new Helpers\kirkiMissing;

//Carregando Actions
use Actions\Actions;

//Carregando Filters
use Filters\Filters;

//Carregando bibliotecas
use Library\ThemeCustomize;

//Carregando Custom Post-Type
new Cpts\ContatoPostType;

define('ROOT', get_template_directory_uri());

class Functions 
{

    /**
     * Singleton
     */
    
    private static $instance;

    /** 
     * Variaveis custom theme supports 
     */
    static $exemplo;

    public static function getInstance() 
    {
        if (self::$instance == NULL):
            self::$instance = new self();
        endif;
    }

    public function __construct() 
    {
        //Adicionando configurações para o thema
        $this->themeSetup();

        //Adicionando Actions
        $this->addActions();

        //Adicionando Filtros
        $this->addFilters();

        //Registrando variaveis
        $this->RegisterVars();

        //Ativando Custom Post-Types 
        acfFields::getInstance();
    }

    /**
     * Configurando Theme support
     */
    public function themeSetup() 
    {
        add_theme_support('post-thumbnails');
        add_theme_support('post-formats', ['video', 'image']);
        add_theme_support('custom-logo');

        //Iniciando theme support customizados
        if( class_exists( 'Kirki' ) )
            ThemeCustomize::getInstance();
    }

    /**
     * Configurando actions
     */
    public function addActions() 
    {
        Actions::getInstance();
    }

    /**
     * Inserindo filtros
     */
    public function addFilters()
    {
        Filters::getInstance();
    }

    public function RegisterVars()
    {
        self::$exemplo = 'Hello Word';
    }

    static public function pagination($wp_query)
    {
        $defaults = array(
            'range'           => 5,
            'custom_query'    => FALSE,
            'previous_string' => __('Anterior'),
            'next_string'     => __('Próxima'),
            'before_output'   => '<ul class="list-unstyled esdep-pagination">',
            'after_output'    => '</ul>'
        );
        
        $args = wp_parse_args( 
            apply_filters( 'wordpressPagination', $defaults )
        );

        $args['range'] = (int) $args['range'] - 1;
        if ( !$args['custom_query'] )
            $args['custom_query'] = $wp_query;
        $count = (int) $args['custom_query']->max_num_pages;
        $page  = intval( get_query_var( 'paged' ) );
        $ceil  = ceil( $args['range'] / 2 );
        
        if ( $count <= 1 )
            return FALSE;
        
        if ( !$page )
            $page = 1;
        
        if ( $count > $args['range'] ) {
            if ( $page <= $args['range'] ) {
                $min = 1;
                $max = $args['range'] + 1;
            } elseif ( $page >= ($count - $ceil) ) {
                $min = $count - $args['range'];
                $max = $count;
            } elseif ( $page >= $args['range'] && $page < ($count - $ceil) ) {
                $min = $page - $ceil;
                $max = $page + $ceil;
            }
        } else {
            $min = 1;
            $max = $count;
        }
        
        $echo = '';
        $previous = intval($page) - 1;
        $previous = esc_attr( get_pagenum_link($previous) );
        $next = intval($page) + 1;
        $next = esc_attr( get_pagenum_link($next) );
        
        $firstpage = esc_attr( get_pagenum_link(1) );
        if ($next && ($page == 1) ) {
            $echo .= '<li class="prev page-numbers inactive">' . $args['previous_string'] . '</li>';
        }

        if ( $previous && (1 != $page) ) {
            $echo .= '<li class="prev"><a href="' . $previous . '" title="' . __( 'anterior') . '">' . $args['previous_string'] . '</a></li>';
        }
        
        if ( !empty($min) && !empty($max) ) {
            for( $i = $min; $i <= $max; $i++ ) {
                if ($page == $i) {
                    $echo .= '<li class="item current">' . str_pad( (int)$i, 2, ' ', STR_PAD_LEFT ) . '</li>';
                } else {
                    $echo .= sprintf( '<li class="item"><a href="%s">%2d</a></li>', esc_attr( get_pagenum_link($i) ), $i );
                }
            }
        }

        if ($next && ($count != $page) ) {
            $echo .= '<li class="next"><a href="' . $next . '" title="' . __( 'próximo') . '">' . $args['next_string'] . '</a></li>';
        }
        if ( $previous && ($count == $page) ) {
            $echo .= '<li class="next">' .  $args['next_string']  . '</li>';
        }
        
        $lastpage = esc_attr( get_pagenum_link($count) );
        if ( isset($echo) )
            echo $args['before_output'] . $echo . $args['after_output'];
    }
    
    /**
     * Pede ID em um vídeo do Youtube
     *
     * @param string $url
     * @return mixed ID do Video ou FALSE se não encontrado
     */
    static public function getYoutubeIdFromUrl($url) 
    {
        $parts = parse_url($url);
        if(isset($parts['query'])){
            parse_str($parts['query'], $qs);
            if(isset($qs['v'])){
                return $qs['v'];
            }else if(isset($qs['vi'])){
                return $qs['vi'];
            }
        }
        if(isset($parts['path'])){
            $path = explode('/', trim($parts['path'], '/'));
            return $path[count($path)-1];
        }
        return false;
    }

}

Functions::getInstance();
