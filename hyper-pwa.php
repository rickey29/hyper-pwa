<?php
/*
Plugin Name: Hyper PWA
Plugin URI:  https://flexplat.com/hyper-pwa/
Description: Converts Accelerated Mobile Pages WordPress into Progressive Web Apps style.
Version:     1.0.0
Author:      Rickey Gu
Author URI:  https://flexplat.com
Text Domain: hyper-pwa
Domain Path: /languages
*/

if ( !defined('ABSPATH') )
{
	exit;
}

class HyperPWA
{
	private $home_url = '';

	private $page_url = '';
	private $plugin_dir_url = '';

	private $home_url_pattern = '';

	private $plugin_dir = '';
	private $plugin_dir_path = '';


	public function __construct()
	{
	}

	public function __destruct()
	{
	}


	private function init()
	{
		$home_url = home_url();
		$this->home_url = preg_replace('/^https?:\/\//im', 'https://', $home_url);

		$parts = parse_url($this->home_url);
		$this->page_url = $parts['scheme'] . '://' . $parts['host'] . add_query_arg(array());
		$this->plugin_dir_url = plugin_dir_url(__FILE__);

		$this->home_url_pattern = str_replace(array('/', '.'), array('\/', '\.'), $this->home_url);

		$this->plugin_dir = preg_replace('/^' . $this->home_url_pattern . '(.+)\/$/im', '${1}', $this->plugin_dir_url);
		$this->plugin_dir_path = plugin_dir_path(__FILE__);
	}


	private function echo_sw_js()
	{
		require_once $this->plugin_dir_path . 'online/fetch.php';

		$fetch = new HyperPWAFetch();

		$data = array(
			'plugin_dir' => $this->plugin_dir
		);

		$page = $fetch->fetch($data);

		if ( empty($page) )
		{
			return;
		}


		header('Content-Type: application/javascript', true);
		echo $page;
	}

	private function echo_sw_html()
	{
		header('Content-Type: text/html; charset=utf-8', true);
		echo '<!doctype html>
<html>
<head>
<title>Installing service worker...</title>
<script type="text/javascript">
	var swsource = "' . $this->home_url . '/hyper-pwa-sw.js";
	if ( "serviceWorker" in navigator ) {
		navigator.serviceWorker.register(swsource).then(function(reg) {
			console.log("ServiceWorker scope: ", reg.scope);
		}).catch(function(err) {
			console.log("ServiceWorker registration failed: ", err);
		});
	};
</script>
</head>
<body>
</body>
</html>';
	}

	private function echo_manifest_webmanifest()
	{
		$name = get_bloginfo('name');
		$description = get_bloginfo('description');

		header('Content-Type: application/x-web-app-manifest+json', true);
		echo '{"name":"' . $name . ( !empty($description) ? (' -- ' . $description) : '' ) . '","short_name":"' . $name . '","start_url":"' . $this->home_url . '/","icons":[{"src":".' . $this->plugin_dir . '/manifest/mf-logo-192.png","sizes":"192x192","type":"image/png","purpose":"any maskable"},{"src":".' . $this->plugin_dir . '/manifest/mf-logo-512.png","sizes":"512x512","type":"image/png"}],"theme_color":"#ffffff","background_color":"#ffffff","display":"standalone"}';
	}


	private function transcode_page($page)
	{
		require_once $this->plugin_dir_path . 'pwa/conversion.php';

		$conversion = new HyperPWAConversion();


		$page = preg_replace('/^[\s\t]*<style type="[^"]+" id="[^"]+"><\/style>$/im', '', $page);

		$data = array(
			'plugin_dir_url' => $this->plugin_dir_url
		);


		$page = $conversion->convert($page, $this->home_url, $data);

		return $page;
	}

	private function catch_page_callback($page)
	{
		if ( empty($page) )
		{
			return;
		}

		$page2 = $this->transcode_page($page);
		if ( empty($page2) )
		{
			return $page;
		}

		return $page2;
	}

	public function after_setup_theme()
	{
		ob_start(array($this, 'catch_page_callback'));
	}

	public function shutdown()
	{
		ob_end_flush();
	}


	public function plugins_loaded()
	{
		$this->init();


		if ( preg_match('/^' . $this->home_url_pattern . '\/hyper-pwa-sw\.js$/im', $this->page_url) )
		{
			$this->echo_sw_js();

			exit();
		}
		elseif ( preg_match('/^' . $this->home_url_pattern . '\/hyper-pwa-sw\.html$/im', $this->page_url) )
		{
			$this->echo_sw_html();

			exit();
		}
		elseif ( preg_match('/^' . $this->home_url_pattern . '\/manifest\.webmanifest$/im', $this->page_url) )
		{
			$this->echo_manifest_webmanifest();

			exit();
		}


		add_action('after_setup_theme', array($this, 'after_setup_theme'));
		add_action('shutdown', array($this, 'shutdown'));
	}
}


$pwa = new HyperPWA();

add_action('plugins_loaded', array($pwa, 'plugins_loaded'));
