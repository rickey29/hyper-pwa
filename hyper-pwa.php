<?php
/*
Plugin Name: Hyper PWA
Plugin URI:  https://flexplat.com/hyper-pwa/
Description: Converts WordPress into Progressive Web Apps style.
Version:     1.3.0
Author:      Rickey Gu
Author URI:  https://flexplat.com
License:     GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain: hyper-pwa
Domain Path: /languages
*/

if ( !defined( 'ABSPATH' ) )
{
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'cfg/cfg.php';

include_once plugin_dir_path( __FILE__ ) . 'admin/admin.php';

class HyperPWA
{
	private $time_now = 0;

	private $home_url = '';
	private $home_url_pattern = '';

	private $page_url = '';

	private $plugin_dir_path = '';
	private $plugin_dir_url = '';
	private $plugin_dir = '';


	public function __construct()
	{
		$this->time_now = time();
	}

	public function __destruct()
	{
	}


	private function init()
	{
		$home_url = home_url();
		$this->home_url = preg_replace( '/^http:\/\//im', 'https://', $home_url );
		$this->home_url_pattern = str_replace( array( '/', '.' ), array( '\/', '\.' ), $this->home_url );

		$parts = parse_url( $this->home_url );
		$this->page_url = $parts['scheme'] . '://' . $parts['host'] . add_query_arg( array() );

		$this->plugin_dir_path = plugin_dir_path( __FILE__ );
		$this->plugin_dir_url = plugin_dir_url( __FILE__ );
		$this->plugin_dir = preg_replace( '/^' . $this->home_url_pattern . '(.+)\/$/im', '${1}', $this->plugin_dir_url );
	}


	private function get_manifest_json()
	{
		require_once $this->plugin_dir_path . 'flx/flx.php';
		$flx = new HyperPWAFlx();

		$manifest_logo_192_url = get_option( HYPER_PWA_APP_ICON );
		if ( !empty($manifest_logo_192_url ) )
		{
			$manifest_logo_192_url = preg_replace( '/^' . $this->home_url_pattern . '(.+)$/im', '.${1}', $manifest_logo_192_url );
		}
		else
		{
			$manifest_logo_192_url = '.' . $this->plugin_dir . '/manifest/logo-192.png';
		}

		$manifest_logo_512_url = get_option( HYPER_PWA_SPLASH_SCREEN_ICON );
		if ( !empty($manifest_logo_512_url ) )
		{
			$manifest_logo_512_url = preg_replace( '/^' . $this->home_url_pattern . '(.+)$/im', '.${1}', $manifest_logo_512_url );
		}
		else
		{
			$manifest_logo_512_url = '.' . $this->plugin_dir . '/manifest/logo-512.png';
		}

		$data = array(
			'name' => get_bloginfo( 'name' ),
			'description' => get_bloginfo( 'description' ),
			'manifest_logo_192_url' => $manifest_logo_192_url,
			'manifest_logo_512_url' => $manifest_logo_512_url
		);

		$page = $flx->get_manifest_json( $this->home_url, $data );
		if ( empty( $page ) )
		{
			return;
		}

		return $page;
	}

	private function get_offline_html()
	{
		require_once $this->plugin_dir_path . 'flx/flx.php';
		$flx = new HyperPWAFlx();

		$page = $flx->get_offline_html( $this->home_url );
		if ( empty( $page ) )
		{
			return;
		}

		return $page;
	}

	public function get_sw_html()
	{
		require_once $this->plugin_dir_path . 'flx/flx.php';
		$flx = new HyperPWAFlx();

		$page = $flx->get_sw_html( $this->home_url );
		if ( empty( $page ) )
		{
			return;
		}

		return $page;
	}

	private function get_sw_js()
	{
		require_once $this->plugin_dir_path . 'flx/flx.php';
		$flx = new HyperPWAFlx();

		$page = $flx->get_sw_js( $this->home_url );
		if ( empty( $page ) )
		{
			return;
		}

		return $page;
	}


	private function update_html( $page )
	{
		require_once $this->plugin_dir_path . 'inc/inc.php';
		$inc = new HyperPWAInc( $this );

		$data = array(
			'plugin_dir_url' => $this->plugin_dir_url
		);

		$inc->init( $this->home_url, $data );
		$page = $inc->update( $page );

		return $page;
	}

	private function catch_page_callback( $page )
	{
		if ( empty( $page ) )
		{
			return;
		}

		$page2 = $this->update_html( $page );
		if ( empty( $page2 ) )
		{
			return $page;
		}

		return $page2;
	}

	public function after_setup_theme()
	{
		ob_start( array( $this, 'catch_page_callback' ) );
	}

	public function shutdown()
	{
		ob_end_flush();
	}


	public function plugins_loaded()
	{
		$this->init();

		if ( preg_match( '/^' . $this->home_url_pattern . '\/hyper-pwa-manifest\.json$/im', $this->page_url ) )
		{
			$page = $this->get_manifest_json();
			if ( empty( $page ) )
			{
				exit();
			}

			header( 'Content-Type: application/x-web-app-manifest+json', TRUE );
			echo $page;

			exit();
		}
		elseif ( preg_match( '/^' . $this->home_url_pattern . '\/hyper-pwa-offline\.html$/im', $this->page_url) )
		{
			$page = $this->get_offline_html();
			if ( empty( $page ) )
			{
				exit();
			}

			header( 'Content-Type: text/html; charset=utf-8', TRUE );
			echo $page;

			exit();
		}
		elseif ( preg_match( '/^' . $this->home_url_pattern . '\/hyper-pwa-sw\.html$/im', $this->page_url) )
		{
			$page = $this->get_sw_html();
			if ( empty( $page ) )
			{
				exit();
			}

			header( 'Content-Type: text/html; charset=utf-8', TRUE );
			echo $page;

			exit();
		}
		elseif ( preg_match( '/^' . $this->home_url_pattern . '\/hyper-pwa-sw\.js$/im', $this->page_url ) )
		{
			$page = $this->get_sw_js();
			if ( empty( $page ) )
			{
				exit();
			}

			header( 'Content-Type: application/javascript', TRUE );
			echo $page;

			exit();
		}


		if ( is_embed() || is_feed() )
		{
			return;
		}
		elseif ( $GLOBALS['pagenow'] === 'admin-ajax.php' || $GLOBALS['pagenow'] === 'wp-activate.php' || $GLOBALS['pagenow'] === 'wp-cron.php' || $GLOBALS['pagenow'] === 'wp-signup.php' )
		{
			return;
		}
		elseif ( is_admin() )
		{
			setcookie( 'hyper-pwa-admin', '1', $this->time_now + DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );

			return;
		}
		elseif ( $GLOBALS['pagenow'] === 'wp-login.php' )
		{
			setcookie( 'hyper-pwa-admin', '', $this->time_now - 1, COOKIEPATH, COOKIE_DOMAIN );

			return;
		}
		elseif ( !empty( $_COOKIE['hyper-pwa-admin'] ) )
		{
			setcookie( 'hyper-pwa-admin', '1', $this->time_now + DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );

			return;
		}

		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
		add_action( 'shutdown', array( $this, 'shutdown' ) );
	}
}


$hyper_pwa = new HyperPWA();

add_action( 'plugins_loaded', array( $hyper_pwa, 'plugins_loaded' ) );
