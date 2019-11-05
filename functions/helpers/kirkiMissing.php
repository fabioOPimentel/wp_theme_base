<?php

namespace Helpers;

if ( !defined( 'ABSPATH' ) ) { exit; };

class kirkiMissing
{
	public function __construct() 
	{
		$this->kirkiMissing();
		$this->kirki_notice_msg = 'É aconselhável o uso do "Kirki Framework" para auxiliar no desenvolvimento do site.';
	}

	public function kirkiMissing()
	{
		if( !class_exists( 'Kirki' ) ) :
			/*
			*	Admin notice
			*/
			add_action( 'admin_notices', array($this, 'tp_notice_missing') );
			
			/*
			*	Frontend notice
			*/
			//add_action( 'template_redirect', array($this,'tp_notice_frontend_missing'),0);

		endif;
	}

	public function tp_notice_missing()
	{
		echo '<div class="notice notice-error notice-large"><div class="notice-title">'. $this->kirki_notice_msg .'</div></div>';
	}

	public function tp_notice_frontend_missing()
	{
		wp_die( $this->kirki_notice_msg );
	}

}