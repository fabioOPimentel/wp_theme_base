<?php

namespace Library;

if ( !defined( 'ABSPATH' ) ) { exit; };

use Kirki;

/**
 * wp-includes/class-wp-customize-manager para emxmplos
 */

class ThemeCustomize 
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

	/**
	 * Identifier, namespace
	 */
	protected $theme_key = '';
	/**
	 * The option value in the database will be based on get_stylesheet()
	 * so child themes don't share the parent theme's option value.
	 */
	protected $option_key = '';
	/**
	 * Initialize
	 *
	 * @param null $args
	 */
	public function __construct( $args = NULL ) 
	{
		// Set option key based on get_stylesheet()
		if ( NULL === $args ) {
			$args[ 'theme_key' ] = strtolower( get_stylesheet() );
		}
		// Set option key based on get_stylesheet()
		$this->theme_key  = $args[ 'theme_key' ];
		$this->option_key = $this->theme_key . '_theme_options';
		// register our custom settings
		add_action( 'customize_register', [ $this, 'CustomizeRegister' ] );
		add_action( 'init', [$this, 'KirkFields'] );

	}

	/**
	 * Implement theme options into Theme Customizer on Frontend
	 *
	 * @param   $wp_customize  Theme Customizer object
	 *
	 * @return  void
	 */
	public function CustomizeRegister( $wp_customize ) 
	{
		// defaults, import for live preview with js helper
		$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

		// ===== Custom Section =====
		// Topo do site
		// $wp_customize->add_section( $this->option_key . '_inicio_', array(
		// 	'title'    => esc_attr__( 'Início'),
		// 	'priority' => 30,
		// ) );
	}
	
	public function KirkFields()
	{
		// Topo do site
		// Kirki::add_field( 'imagem_destaque', [
		// 	'type'        => 'image',
		// 	'settings'    => 'imagem_destaque',
		// 	'label'       => esc_html__( 'Imagem de destaque', 'kirki' ),
		// 	'description' => esc_html__( 'Defina a imagem de destaque para o topo', 'kirki' ),
		// 	'section'     => $this->option_key . '_inicio_',
		// 	'priority' => 10,
		// 	'choices'     => [
		// 		'save_as' => 'array',
		// 	],
		// ] );
	}
} // end class