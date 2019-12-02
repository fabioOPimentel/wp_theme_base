<?php

namespace Actions;

if ( !defined( 'ABSPATH' ) ) { exit; };

//Carregando bibliotecas
use Library\RegistrandoMenus;
use Library\EnfileirandoEstilos;
use Library\EnviandoContato;
use Library\WpHtmlCompression;
use Library\Gcaptcha;

class Actions
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
        
        /* Enviando Contatos com anexo*/
        add_action("wp_ajax_nopriv_enviar_contato_anexo",array(new EnviandoContato, 'sendContactFormAtt'));
        add_action("wp_ajax_enviar_contato_anexo", array(new EnviandoContato, 'sendContactFormAtt'));

        /* Verificando Captcha */
        add_action("wp_ajax_nopriv_gCaptcha",array(new Gcaptcha, 'verifyCaptcha'));
        add_action("wp_ajax_gCaptcha", array(new Gcaptcha, 'verifyCaptcha'));

        /* Comprimindo codigo HTML */
        //add_action('after_setup_theme', array($this, 'wp_html_compression_start'));
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

    /**
     * Declarando caminhos padrõs a serem acessados 
     * por JavaScript 
     */
    public function ScriptsBaseURL()
    {
        wp_localize_script( 'template-js', 'baseUrl', array(
            'templateUrl' => get_template_directory_uri(),
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

    static function wp_html_compression_finish($html) 
    {
        return new WP_HTML_Compression($html);
    }

    public function wp_html_compression_start() 
    {
        ob_start('self::wp_html_compression_finish');
    }
}