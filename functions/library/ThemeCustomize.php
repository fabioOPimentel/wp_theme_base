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
	public function __construct( $args = NULL ) {
		// Set option key based on get_stylesheet()
		if ( NULL === $args ) {
			$args[ 'theme_key' ] = strtolower( get_stylesheet() );
		}
		// Set option key based on get_stylesheet()
		$this->theme_key  = $args[ 'theme_key' ];
		$this->option_key = $this->theme_key . '_theme_options';
		// register our custom settings
		add_action( 'customize_register', array( $this, 'customize_register' ) );
		// Scripts for Preview
		//add_action( 'customize_preview_init', array( $this, 'customize_preview_js' ) );
	}

	/**
	 * Implement theme options into Theme Customizer on Frontend
	 *
	 * @param   $wp_customize  Theme Customizer object
	 *
	 * @return  void
	 */
	public function customize_register( $wp_customize ) {
		// defaults, import for live preview with js helper
		$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
        $wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
        
		// ===== Custom Section =====
		// create custom section for custom logos
		$wp_customize->add_section( $this->option_key . '_rewrite_url', array(
			'title'    => esc_attr__( 'Logos', 'documentation' ),
			'priority' => 35,
		) );
		
		$wp_customize->add_setting(
			'site_icon',
			array(
				'type'       => 'option',
				'capability' => 'manage_options',
				'transport'  => 'postMessage', // Previewed with JS in the Customizer controls window.
			)
        );
		$wp_customize->add_control(
			new \WP_Customize_Site_Icon_Control(
				$wp_customize,
				'site_icon',
				array(
					'label'       => __( 'Site Icon' ),
					'description' => sprintf(
						'<p>' . __( 'Site Icons are what you see in browser tabs, bookmark bars, and within the WordPress mobile apps. Upload one here!' ) . '</p>' .
						/* translators: %s: site icon size in pixels */
						'<p>' . __( 'Site Icons should be square and at least %s pixels.' ) . '</p>',
						'<strong>512 &times; 512</strong>'
					),
					'section'     => $this->option_key . '_rewrite_url',
					'priority'    => 60,
					'height'      => 512,
					'width'       => 512,
				)
			)
		);
        
        $wp_customize->add_setting(
			'logo_teste',
			array(
				'type'       => 'option',
				'capability' => 'manage_options',
				'transport'  => 'postMessage', // Previewed with JS in the Customizer controls window.
			)
		);
		$wp_customize->add_control( 
            new \WP_Customize_Cropped_Image_Control( 
                $wp_customize, 
                'logo_teste',
				array(
					'label'         => __( 'Logo ESDEP' ),
					'section'       => $this->option_key . '_rewrite_url',
					'priority'      => 8,
					'height'        => 60,
					'width'         => 174,
					'flex_height'   => true,
					'flex_width'    => true,
					'button_labels' => array(
						'select'       => __( 'Select logo' ),
						'change'       => __( 'Change logo' ),
						'remove'       => __( 'Remove' ),
						'default'      => __( 'Default' ),
						'placeholder'  => __( 'No logo selected' ),
						'frame_title'  => __( 'Select logo' ),
						'frame_button' => __( 'Choose logo' ),
					),
				)
			)
		);
		
		// create custom section for footer information
		$wp_customize->add_section( $this->option_key . '_rodape_', array(
			'title'    => esc_attr__( 'Rodapé'),
			'priority' => 35,
		) );

		$wp_customize->add_setting(
			'teste',
			array(
				'type'       => 'option',
				'capability' => 'manage_options',
			)
		);
		/**
		 * Control type. Core controls include 'text', 'checkbox',
		* 'textarea', 'radio', 'select', and 'dropdown-pages'. Additional
		* input types such as 'email', 'url', 'number', 'hidden', and
		* 'date' are supported implicitly. Default 'text'.
		 */
		$wp_customize->add_control(
			'teste',
			array(
				'label'   => __( 'Número da Central de Atendimento' ),
				'section' => $this->option_key . '_rodape_',
				'type' => 'text'
			)
		);
	}

	/**
	 * Mp reload for changes
	 * 
	 * @return   void
	 */
	public function customize_preview_js() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.dev' : '';
		wp_register_script(
			$this->theme_key . '-customizer',
			get_template_directory_uri() . '/js/theme-customizer' . $suffix . '.js',
			array( 'customize-preview' ),
			FALSE,
			TRUE
		);
		wp_enqueue_script( $this->theme_key . '-customizer' );
	}
} // end class