<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
{
	exit;
}

global $wpdb;

$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'hyper_pwa_%'" );
