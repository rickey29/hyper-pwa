<?php
/*
Plugin Name: Hyper PWA
Plugin URI:  https://flexplat.com/hyper-pwa/
Description: Converts Accelerated Mobile Pages WordPress into Progressive Web Apps style.
Version:     1.1.0
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


	private function echo_manifest_webmanifest()
	{
		require_once $this->plugin_dir_path . 'flx/flx.php';

		$flx = new HyperPWAFlx();

		$data = array(
			'name' => get_bloginfo( 'name' ),
			'description' => get_bloginfo( 'description' ),
			'plugin_dir' => $this->plugin_dir
		);

		$page = $flx->get_manifest_webmanifest( $this->home_url, $data );
		if ( empty( $page ) )
		{
			return;
		}

		header( 'Content-Type: application/x-web-app-manifest+json', TRUE );
		echo $page;
	}

	private function echo_sw_html()
	{
		require_once $this->plugin_dir_path . 'flx/flx.php';

		$flx = new HyperPWAFlx();

		$page = $flx->get_sw_html( $this->home_url );
		if ( empty( $page ) )
		{
			return;
		}

		header( 'Content-Type: text/html; charset=utf-8', TRUE );
		echo $page;
	}

	private function echo_sw_js()
	{
		require_once $this->plugin_dir_path . 'flx/flx.php';

		$flx = new HyperPWAFlx();

		$data = array(
			'plugin_dir' => $this->plugin_dir
		);

		$page = $flx->get_sw_js( $this->home_url, $data );
		if ( empty( $page ) )
		{
			return;
		}

		header( 'Content-Type: application/javascript', TRUE );
		echo $page;
	}


	private function transcode_page( $page )
	{
		require_once $this->plugin_dir_path . 'transcoding/transcoding.php';

		$transcoding = new HyperPWATranscoding();

		$page = preg_replace( '/^[\s\t]*<style type="[^"]+" id="[^"]+"><\/style>$/im', '', $page );

		$data = array(
			'plugin_dir_url' => $this->plugin_dir_url
		);

		$page = $transcoding->transcode( $page, $this->home_url, $data );

		return $page;
	}

	private function catch_page_callback( $page )
	{
		if ( empty( $page ) )
		{
			return;
		}

		$page2 = $this->transcode_page( $page );
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
			setcookie( 'hyper_pwa_admin', '1', $this->time_now + DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );

			return;
		}
		elseif ( $GLOBALS['pagenow'] === 'wp-login.php' )
		{
			setcookie( 'hyper_pwa_admin', '', $this->time_now - 1, COOKIEPATH, COOKIE_DOMAIN );

			return;
		}
		elseif ( !empty( $_COOKIE['hyper_pwa_admin'] ) )
		{
			setcookie( 'hyper_pwa_admin', '1', $this->time_now + DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );

			return;
		}


		$this->init();


		if ( preg_match( '/^' . $this->home_url_pattern . '\/manifest\.webmanifest$/im', $this->page_url ) )
		{
			$this->echo_manifest_webmanifest();

			exit();
		}
		elseif ( preg_match( '/^' . $this->home_url_pattern . '\/hyper-pwa-sw\.html$/im', $this->page_url) )
		{
			$this->echo_sw_html();

			exit();
		}
		elseif ( preg_match( '/^' . $this->home_url_pattern . '\/hyper-pwa-sw\.js$/im', $this->page_url ) )
		{
			$this->echo_sw_js();

			exit();
		}


		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
		add_action( 'shutdown', array( $this, 'shutdown' ) );
	}
}


$pwa = new HyperPWA();

add_action( 'plugins_loaded', array( $pwa, 'plugins_loaded' ) );
