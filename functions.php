<?php

require_once 'functions/security.php';

//Carregando autoloader do composer
require 'vendor/autoload.php';

//Carregando autoloader do tema
require "inc/autoloadApp.php";
require "inc/autoloadCpts.php";

use App\RegistrandoMenus;
use App\EnfileirandoEstilos;
use App\EnviandoContato;
use App\WpHtmlCompression;

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
        
        add_action('init', array(new RegistrandoMenus, 'registrandoMenu'));
        
        add_action('wp_enqueue_scripts', array(new EnfileirandoEstilos, 'enfileirandoEstilo'));

        add_action( 'admin_menu', array($this, 'removeMenuItens'));

        //***************************
        //Conexões Ajax
        //***************************

        /* Enviando Contatos */
        add_action("wp_ajax_nopriv_enviar_contato",array(new EnviandoContato, 'sendContactForm'));
        add_action("wp_ajax_enviar_contato", array(new EnviandoContato, 'sendContactForm'));

        /* Comprimindo codigo HTML */
        //add_action('after_setup_theme', array($this, 'wp_html_compression_start'));
    }

    /**
     * Inserindo filtros
     */
    public function addFilters()
    {
        //Insira seus filtros aqui
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

    public function wp_html_compression_start() 
    {
        ob_start('self::wp_html_compression_finish');
    }

}

Functions::getInstance();
