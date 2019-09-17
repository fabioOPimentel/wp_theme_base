<?php

namespace Helpers;

if ( !defined( 'ABSPATH' ) ) { exit; };

class acfMissing
{
	public function __construct() 
	{
		$this->acfMissing();
		$this->acf_notice_msg = 'Esse site precisa do "Advanced Custom Fields Pro" para funcionar. Por favor efetue o download e o ative';
	}

	public function acfMissing()
	{
		if( !class_exists('ACF') ) :
			/*
			*	Admin notice
			*/
			add_action( 'admin_notices', array($this, 'tp_notice_missing_acf') );
			
			/*
			*	Frontend notice
			*/
			add_action( 'template_redirect', array($this,'tp_notice_frontend_missing_acf'),0);
			
		else:
			/*
			*	Mask ACF in WordPress Admin Menu
			* /!\ Change 'MY_USER_LOGIN_ON_THIS_WEBSITE' to your login
			*/
			add_action( 'plugins_loaded', array($this,'tp_mask_acf'));
		endif;
	}

	public function tp_notice_missing_acf()
	{
		echo '<div class="notice notice-error notice-large"><div class="notice-title">'. $this->acf_notice_msg .'</div></div>';
	}

	public function tp_notice_frontend_missing_acf()
	{
		wp_die( $this->acf_notice_msg );
	}

	public function tp_mask_acf()
	{
		$current_user = wp_get_current_user();
		
		if( 'MY_USER_LOGIN_ON_THIS_WEBSITE' != $current_user->user_login ):
			define( 'ACF_PRO' , true );
		endif;
	}

}