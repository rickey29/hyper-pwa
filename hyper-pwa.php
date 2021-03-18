<?php
/*
Plugin Name: Hyper PWA
Plugin URI:  https://flexplat.com/hyper-pwa/
Description: Converts WordPress into Progressive Web Apps style.
Version:     1.2.0
Author:      Rickey Gu
Author URI:  https://flexplat.com
Text Domain: hyper-pwa
Domain Path: /languages
*/

if ( !defined( 'ABSPATH' ) )
{
	exit;
}

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

		$data = array(
			'name' => get_bloginfo( 'name' ),
			'description' => get_bloginfo( 'description' ),
			'plugin_dir' => $this->plugin_dir
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

	private function get_sw_html()
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


	private function catch_page_callback( $page )
	{
		if ( empty( $page ) )
		{
			return;
		}


		$head = '';
		if ( preg_match( '/<html\b[^>]* amp\b[^>]*>/i', $page ) )
		{
			if ( !preg_match( '/<script async custom-element="amp-install-serviceworker" src="[^"]*"><\/script>/i', $page ) )
			{
				$head = '<script async custom-element="amp-install-serviceworker" src="https://cdn.ampproject.org/v0/amp-install-serviceworker-0.1.js"></script>';
			}
		}
		else
		{
			$page2 = $this->get_sw_html();
			if ( preg_match( '/<script\b[^>]*>.+<\/script>/isU', $page2, $matches ) )
			{
				$head = $matches[0];
			}
		}

		if ( !empty( $head ) )
		{
			$page = preg_replace( '/(<head\b[^>]*>)/i', '${1}' . "\n" . $head, $page, 1 );
		}


		$head = '<link rel="manifest" href="' . $this->home_url . '/manifest.json" />
<meta name="theme-color" content="#ffffff" />
<link rel="apple-touch-icon" href="' . $this->plugin_dir_url . 'manifest/mf-logo-192.png" />';

		$page = preg_replace( '/<\/head>/i', $head . "\n" . '</head>', $page, 1 );


		if ( preg_match( '/<html\b[^>]* amp\b[^>]*>/i', $page ) )
		{
			$body = '<amp-install-serviceworker src="' . $this->home_url . '/hyper-pwa-sw.js" data-iframe-src="' . $this->home_url . '/hyper-pwa-sw.html" layout="nodisplay"></amp-install-serviceworker>';

			$page = preg_replace( '/<\/body>/i', $body . "\n" . '</body>', $page, 1 );
		}

		return $page;
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


		$this->init();


		if ( preg_match( '/^' . $this->home_url_pattern . '\/manifest\.json$/im', $this->page_url ) )
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
		elseif ( preg_match( '/^' . $this->home_url_pattern . '\/offline\.html$/im', $this->page_url) )
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


		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
		add_action( 'shutdown', array( $this, 'shutdown' ) );
	}
}


$pwa = new HyperPWA();

add_action( 'plugins_loaded', array( $pwa, 'plugins_loaded' ) );
