<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
{
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'cfg/cfg.php';

delete_option( HYPER_PWA_APP_ICON );
delete_option( HYPER_PWA_SPLASH_SCREEN_ICON );
delete_option( HYPER_PWA_NAME );
delete_option( HYPER_PWA_SHORT_NAME );
delete_option( HYPER_PWA_DESCRIPTION );
delete_option( HYPER_PWA_SITE_TYPE );
