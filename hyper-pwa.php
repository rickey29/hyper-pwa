<?php
/*
Plugin Name: Hyper PWA
Plugin URI:  https://flexplat.com/hyper-pwa/
Description: Converts WordPress into Progressive Web Apps style.
Version:     1.8.0
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
	private $host_dir = '';

	private $page_url = '';

	private $plugin_dir = '';
	private $plugin_dir_path = '';


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
		$host_url = preg_replace('/^(https:\/\/.+)\/.*$/imU', '${1}', $this->home_url);
		$host_url_pattern = str_replace( array( '/', '.' ), array( '\/', '\.' ), $host_url );
		$this->host_dir = preg_replace( '/^' . $host_url_pattern . '(.*)$/im', '${1}', $this->home_url );

		$parts = parse_url( $this->home_url );
		$this->page_url = $parts['scheme'] . '://' . $parts['host'] . add_query_arg( array() );

		$plugin_dir_url = plugin_dir_url( __FILE__ );
		$this->plugin_dir = preg_replace( '/^' . $this->home_url_pattern . '(.+)\/$/im', '${1}', $plugin_dir_url );
		$this->plugin_dir_path = plugin_dir_path( __FILE__ );
	}


	private function get_manifest_json()
	{
		require_once $this->plugin_dir_path . 'flx/flx.php';
		$flx = new HyperPWAFlx();

		$short_name = get_option( HYPER_PWA_SHORT_NAME );
		if ( empty( $short_name ) )
		{
			$short_name = get_bloginfo( 'name' );
		}

		$description = get_option( HYPER_PWA_DESCRIPTION );
		if ( empty( $description ) )
		{
			$description = get_bloginfo( 'description' );
		}

		$name = get_option( HYPER_PWA_NAME );
		if ( empty( $name ) )
		{
			$name = $short_name . ( !empty( $description ) ? ( ' -- ' . $description ) : '' );
		}

		$manifest_logo_192_url = get_option( HYPER_PWA_APP_ICON );
		if ( !empty( $manifest_logo_192_url ) )
		{
			$manifest_logo_192_url = preg_replace( '/^' . $this->home_url_pattern . '(.+)$/im', '${1}', $manifest_logo_192_url );
		}
		else
		{
			$manifest_logo_192_url = $this->plugin_dir . '/manifest/logo-192.png';
		}

		$manifest_logo_512_url = get_option( HYPER_PWA_SPLASH_SCREEN_ICON );
		if ( !empty( $manifest_logo_512_url ) )
		{
			$manifest_logo_512_url = preg_replace( '/^' . $this->home_url_pattern . '(.+)$/im', '${1}', $manifest_logo_512_url );
		}
		else
		{
			$manifest_logo_512_url = $this->plugin_dir . '/manifest/logo-512.png';
		}

		$data = array(
			'name' => $name,
			'short_name' => $short_name,
			'manifest_logo_192_url' => $manifest_logo_192_url,
			'manifest_logo_512_url' => $manifest_logo_512_url
		);

		if ( !empty( $description ) )
		{
			$data = array_merge( $data, array( 'description' => $description ) );
		}

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

	public function get_service_worker_html()
	{
		require_once $this->plugin_dir_path . 'flx/flx.php';
		$flx = new HyperPWAFlx();

		$page = $flx->get_service_worker_html( $this->home_url );
		if ( empty( $page ) )
		{
			return;
		}

		return $page;
	}

	private function get_service_worker_js()
	{
		require_once $this->plugin_dir_path . 'flx/flx.php';
		$flx = new HyperPWAFlx();

		$site_type = get_option( HYPER_PWA_SITE_TYPE );
		if ( !empty( $site_type[0] ) )
		{
			$site_type = $site_type[0];
		}
		else
		{
			$site_type = 'else';
		}

		$data = array(
			'site_type' => $site_type
		);

		$page = $flx->get_service_worker_js( $this->home_url, $data );
		if ( empty( $page ) )
		{
			return;
		}

		return $page;
	}

	public function get_service_worker_unregister_html()
	{
		require_once $this->plugin_dir_path . 'flx/flx.php';
		$flx = new HyperPWAFlx();

		$page = $flx->get_service_worker_unregister_html( $this->home_url );
		if ( empty( $page ) )
		{
			return;
		}

		return $page;
	}


	private function get_page_type()
	{
		global $wp_query;

		$page_type = '';
		if ( $wp_query->is_page )
		{
			$page_type = is_front_page() ? 'front' : 'page';
		}
		elseif ( $wp_query->is_home )
		{
			$page_type = 'home';
		}
		elseif ( $wp_query->is_single )
		{
			$page_type = ( $wp_query->is_attachment ) ? 'attachment' : 'single';
		}
		elseif ( $wp_query->is_category )
		{
			$page_type = 'category';
		}
		elseif ( $wp_query->is_tag )
		{
			$page_type = 'tag';
		}
		elseif ( $wp_query->is_tax )
		{
			$page_type = 'tax';
		}
		elseif ( $wp_query->is_archive )
		{
			if ( $wp_query->is_day )
			{
				$page_type = 'day';
			}
			elseif ( $wp_query->is_month )
			{
				$page_type = 'month';
			}
			elseif ( $wp_query->is_year )
			{
				$page_type = 'year';
			}
			elseif ( $wp_query->is_author )
			{
				$page_type = 'author';
			}
			else
			{
				$page_type = 'archive';
			}
		}
		elseif ( $wp_query->is_search )
		{
			$page_type = 'search';
		}
		elseif ( $wp_query->is_404 )
		{
			$page_type = 'notfound';
		}

		return $page_type;
	}

	private function service_worker_callback( $page )
	{
		if ( !preg_match( '/<!DOCTYPE html>/i', $page ) )
		{
			return $page;
		}

		require_once $this->plugin_dir_path . 'inc/inc.php';
		$inc = new HyperPWAInc( $this );

		$manifest_logo_192_url = get_option( HYPER_PWA_APP_ICON );
		if ( !empty( $manifest_logo_192_url ) )
		{
			$manifest_logo_192_url = preg_replace( '/^' . $this->home_url_pattern . '(.+)$/im', '${1}', $manifest_logo_192_url );
		}
		else
		{
			$manifest_logo_192_url = $this->plugin_dir . '/manifest/logo-192.png';
		}

		$page_type = $this->get_page_type();

		$page2 = $inc->add_service_worker( $page, $this->host_dir, $manifest_logo_192_url, $page_type );
		if ( empty( $page2 ) )
		{
			return $page;
		}

		return $page2;
	}

	private function service_worker_unregister_callback( $page )
	{
		if ( !preg_match( '/<!DOCTYPE html>/i', $page ) )
		{
			return $page;
		}

		require_once $this->plugin_dir_path . 'inc/inc.php';
		$inc = new HyperPWAInc( $this );

		$page2 = $inc->add_service_worker_unregister( $page );
		if ( empty( $page2 ) )
		{
			return $page;
		}

		return $page2;
	}

	public function after_setup_theme()
	{
		if ( empty( $_COOKIE['hyper-pwa-admin'] ) )
		{
			ob_start( array( $this, 'service_worker_callback' ) );
		}
		else
		{
			ob_start( array( $this, 'service_worker_unregister_callback' ) );
		}
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
		elseif ( preg_match( '/^' . $this->home_url_pattern . '\/hyper-pwa-service-worker\.html$/im', $this->page_url) )
		{
			$page = $this->get_service_worker_html();
			if ( empty( $page ) )
			{
				exit();
			}

			header( 'Content-Type: text/html; charset=utf-8', TRUE );
			echo $page;

			exit();
		}
		elseif ( preg_match( '/^' . $this->home_url_pattern . '\/hyper-pwa-service-worker\.js$/im', $this->page_url ) )
		{
			$page = $this->get_service_worker_js();
			if ( empty( $page ) )
			{
				exit();
			}

			header( 'Content-Type: application/javascript', TRUE );
			echo $page;

			exit();
		}
		elseif ( preg_match( '/^' . $this->home_url_pattern . '\/hyper-pwa-service-worker-unregister\.html$/im', $this->page_url) )
		{
			$page = $this->get_service_worker_unregister_html();
			if ( empty( $page ) )
			{
				exit();
			}

			header( 'Content-Type: text/html; charset=utf-8', TRUE );
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
		elseif ( $GLOBALS['pagenow'] === 'wp-login.php' )
		{
			if ( !empty( $_COOKIE['hyper-pwa-admin'] ) )
			{
				delete_transient( HYPER_PWA_MANIFEST_JSON );
				delete_transient( HYPER_PWA_OFFLINE_HTML );
				delete_transient( HYPER_PWA_SERVICE_WORKER_HTML );
				delete_transient( HYPER_PWA_SERVICE_WORKER_JS );
			}

			setcookie( 'hyper-pwa-admin', '', $this->time_now - 1, COOKIEPATH, COOKIE_DOMAIN );

			return;
		}
		elseif ( is_admin() )
		{
			if ( empty( $_COOKIE['hyper-pwa-admin'] ) )
			{
				delete_transient( HYPER_PWA_SERVICE_WORKER_UNREGISTER_HTML );
			}

			setcookie( 'hyper-pwa-admin', '1', $this->time_now + DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
		}
		elseif ( !empty( $_COOKIE['hyper-pwa-admin'] ) )
		{
			setcookie( 'hyper-pwa-admin', '1', $this->time_now + DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
		}

		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
		add_action( 'shutdown', array( $this, 'shutdown' ) );
	}


	public function register_activation()
	{
		setcookie( 'hyper-pwa-admin', '1', $this->time_now + DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
	}

	public function register_deactivation()
	{
		setcookie( 'hyper-pwa-admin', '', $this->time_now - 1, COOKIEPATH, COOKIE_DOMAIN );

		delete_transient( HYPER_PWA_MANIFEST_JSON );
		delete_transient( HYPER_PWA_OFFLINE_HTML );
		delete_transient( HYPER_PWA_SERVICE_WORKER_HTML );
		delete_transient( HYPER_PWA_SERVICE_WORKER_JS );
		delete_transient( HYPER_PWA_SERVICE_WORKER_UNREGISTER_HTML );
	}


	public function settings_link( $links )
	{
		$url = esc_url( add_query_arg( 'page', 'hyper-pwa', get_admin_url() . 'admin.php' ) );
		$settings_link = '<a href="' . $url . '">' . __( 'Settings' ) . '</a>';
		array_push(
			$links,
			$settings_link
		);

		return $links;
	}
}


$hyper_pwa = new HyperPWA();

add_action( 'plugins_loaded', array( $hyper_pwa, 'plugins_loaded' ) );

register_activation_hook( __FILE__, array( $hyper_pwa, 'register_activation' ) );
register_deactivation_hook( __FILE__, array( $hyper_pwa, 'register_deactivation' ) );

add_filter( 'plugin_action_links_hyper-pwa/hyper-pwa.php', array( $hyper_pwa, 'settings_link' ) );
