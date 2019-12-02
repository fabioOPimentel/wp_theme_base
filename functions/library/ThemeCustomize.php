<?php

namespace Library;

if ( !defined( 'ABSPATH' ) ) { exit; };

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
		add_action( 'customize_register', array( $this, 'customize_register' ) );

	}

	/**
	 * Implement theme options into Theme Customizer on Frontend
	 *
	 * @param   $wp_customize  Theme Customizer object
	 *
	 * @return  void
	 */
	public function customize_register( $wp_customize ) 
	{
		/**
		 * Control type. Core controls include 'text', 'checkbox',
		* 'textarea', 'radio', 'select', and 'dropdown-pages'. Additional
		* input types such as 'email', 'url', 'number', 'hidden', and
		* 'date' are supported implicitly. Default 'text'.
		 */

		// defaults, import for live preview with js helper
		$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

		// ===== Custom Section =====
		// Exemplo
		// $wp_customize->add_section( $this->option_key . '_webmail_', array(
		// 	'title'    => esc_attr__( 'Webmail'),
		// 	'priority' => 33,
		// ) );

		// \Kirki::add_field( 'webmail', [
		// 	'type'     => 'link',
		// 	'settings' => 'webmail',
		// 	'label'    => __( 'Webmail', 'kirki' ),
		// 	'section'  => $this->option_key . '_webmail_',
		// ] );

	}
} // end class