<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('ACF') ) :
	$tp_acf_notice_msg = __( 'Esse site precisa do "Advanced Custom Fields Pro" para funcionar. Por favor efetue o download e o ative', 'tp-notice-acf' );
	
	/*
	*	Admin notice
	*/
	add_action( 'admin_notices', 'tp_notice_missing_acf' );
	function tp_notice_missing_acf()
	{
		global $tp_acf_notice_msg;
		
		echo '<div class="notice notice-error notice-large"><div class="notice-title">'. $tp_acf_notice_msg .'</div></div>';
	}
	/*
	*	Frontend notice
	*/
	add_action( 'template_redirect', 'tp_notice_frontend_missing_acf', 0 );
	function tp_notice_frontend_missing_acf()
	{
		global $tp_acf_notice_msg;
		
		wp_die( $tp_acf_notice_msg );
    }
    
else:

	/*
	*	Mask ACF in WordPress Admin Menu
	* /!\ Change 'MY_USER_LOGIN_ON_THIS_WEBSITE' to your login
	*/
	add_action( 'plugins_loaded', 'tp_mask_acf' );
	function tp_mask_acf()
	{
		$current_user = wp_get_current_user();
		
		if( 'MY_USER_LOGIN_ON_THIS_WEBSITE' != $current_user->user_login ):
			define( 'ACF_PRO' , true );
        endif;
    }
    
endif;