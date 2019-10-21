<?php

if ( !defined( 'ABSPATH' ) ) { exit; };

//Carregando autoloader do composer
require 'vendor/autoload.php';

//Carregando helpers
use Helpers\acfFields;
new Helpers\acfMissing;

//Carregando bibliotecas
use Library\RegistrandoMenus;
use Library\EnfileirandoEstilos;
use Library\EnviandoContato;
use Library\WpHtmlCompression;
use Library\Gcaptcha;

//Carregando Custom Post-Type
new Cpts\ContatoPostType;


class Functions 
{

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
        //Adicionando configurações para o thema
        $this->themeSetup();

        //Adicionando Actions
        $this->addActions();

        //Adicionando Filtros
        $this->addFilters();

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
    }

    /**
     * Configurando actions
     */
    public function addActions() 
    {
        //add_action( 'phpmailer_init', array($this, 'send_smtp_email') );
        
        add_action('init', array(new RegistrandoMenus, 'registrandoMenu'));
        
        add_action('wp_enqueue_scripts', array(new EnfileirandoEstilos, 'enfileirandoEstilo'));

        add_action( 'admin_menu', array($this, 'removeMenuItens'));

        add_action( 'wp_enqueue_scripts', array($this,'ScriptsBaseURL' ));

        //add_action( 'login_enqueue_scripts', array($this, 'myLoginLogo' ));

        //***************************
        //Conexões Ajax
        //***************************

        /* Enviando Contatos */
        add_action("wp_ajax_nopriv_enviar_contato",array(new EnviandoContato, 'sendContactForm'));
        add_action("wp_ajax_enviar_contato", array(new EnviandoContato, 'sendContactForm'));

        /* Verificando Captcha */
        add_action("wp_ajax_nopriv_gCaptcha",array(new Gcaptcha, 'verifyCaptcha'));
        add_action("wp_ajax_gCaptcha", array(new Gcaptcha, 'verifyCaptcha'));

        /* Comprimindo codigo HTML */
        //add_action('after_setup_theme', array($this, 'wp_html_compression_start'));
    }

    /**
     * Inserindo filtros
     */
    public function addFilters()
    {
        //Insira seus filtros aqui
        add_filter( 'slug', array($this, 'slugfy') );
        /** Disabilitando Gutemberg */
        // disabilitando para posts
        //add_filter('use_block_editor_for_post', '__return_false', 10);

        // disabilitando para post types
        //add_filter('use_block_editor_for_post_type', '__return_false', 10);

        // desabilitando para páginas com base no id
        // add_filter('use_block_editor_for_post_type', function ($is_enabled){
        //     return $this->disableGutembergToPageById($is_enabled, 31);
        // }, 10, 2);
    }

    public function disableGutembergToPageById($is_enabled, $pageID)
    {
        
        if ( get_the_ID() === $pageID ) return false;
        
        return $is_enabled;
        
    }

    /**
	 * Filtro Slugfy
	 */

	public function slugfy($string) 
	{
		$table = array(
		'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
		'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
		'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
		'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
		'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
		'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
		'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
		'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r', '/' => '-', ' ' => '-'
		);

		// -- Remove duplicated spaces
		$stripped = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $string);

		// -- Returns the slug
		return strtolower(strtr($string, $table));
    }

    /**
     * Removendo visualição de itens menu administrativo
     */

    public function removeMenuItens()
    {
        $current_user = wp_get_current_user();
        $username = $current_user->user_login;

        //remove_menu_page( 'edit.php?post_type=cfs' );
    }

    static function wp_html_compression_finish($html) 
    {
        return new WP_HTML_Compression($html);
    }

    public function ScriptsBaseURL()
    {
        wp_localize_script( 'template-js', 'baseUrl', array(
            'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php', // WordPress AJAX
        ) );
    }

    public function myLoginLogo() 
    { 
        echo "<style type='text/css'>
            #login h1 a, .login h1 a {
                background-image: url(". get_template_directory_uri() .");
                height: 65px;
                width: 320px;
                background-size: 70%;
                background-repeat: no-repeat;
                background-position: center;
                padding-bottom: 30px;
            }
        </style>";
    }

    public function send_smtp_email( $phpmailer ) 
    {
        $phpmailer->isSMTP();
        $phpmailer->Host       = '';
        $phpmailer->SMTPAuth   = true;
        $phpmailer->Port       = 465;
        $phpmailer->Username   = '';
        $phpmailer->Password   = '';
        $phpmailer->SMTPSecure = 'ssl';
        $phpmailer->From       = '';
        $phpmailer->FromName   = '';
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

    public function wp_html_compression_start() 
    {
        ob_start('self::wp_html_compression_finish');
    }

}

Functions::getInstance();
