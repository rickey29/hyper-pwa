<?php
if ( !defined('ABSPATH') )
{
	exit;
}

class HyperPWATranscoding
{
	private $home_url = '';

	private $plugin_dir_url = '';


	public function __construct()
	{
	}

	public function __destruct()
	{
	}


	public function init($home_url, $data)
	{
		$this->home_url = preg_replace('/\/$/im', '', $home_url);

		if ( !empty($data['plugin_dir_url']) && is_string($data['plugin_dir_url']) )
		{
			$this->plugin_dir_url = $data['plugin_dir_url'];
		}
	}

	public function transcode_html($page)
	{
		// Service Workers
		$body = '<amp-install-serviceworker src="' . $this->home_url . '/hyper-pwa-sw.js" data-iframe-src="' . $this->home_url . '/hyper-pwa-sw.html" layout="nodisplay"></amp-install-serviceworker>';

		$page = preg_replace('/<\/body>/i', $body . "\n" . '</body>', $page, 1);

		return $page;
	}

	public function transcode_head($page)
	{
		// Progressive Web Apps
		$head = '<meta name="theme-color" content="#ffffff" />';
		$head .= "\n" . '<link rel="manifest" href="' . $this->home_url . '/manifest.webmanifest" />';
		$head .= "\n" . '<link rel="apple-touch-icon" href="' . $this->plugin_dir_url . 'pwa/manifest/mf-logo-192.png" />';

		// The tag 'amp-install-serviceworker' requires including the 'amp-install-serviceworker' extension JavaScript.
		if ( !preg_match('/<script async custom-element="amp-install-serviceworker" src="[^"]*"><\/script>/i', $page) )
		{
			$head .= "\n" . '<script async custom-element="amp-install-serviceworker" src="https://cdn.ampproject.org/v0/amp-install-serviceworker-0.1.js"></script>';
		}

		$page = preg_replace('/<\/head>/i', $head . "\n" . '</head>', $page, 1);

		return $page;
	}
}
