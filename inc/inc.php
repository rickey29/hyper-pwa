<?php
if ( !defined( 'ABSPATH' ) )
{
	exit;
}

class HyperPWAInc
{
	private $hyper_pwa = NULL;

	private $home_url = '';

	private $plugin_dir_url = '';


	public function __construct( $hyper_pwa )
	{
		$this->hyper_pwa = $hyper_pwa;
	}

	public function __destruct()
	{
	}


	public function init( $home_url, $data )
	{
		$this->home_url = $home_url;

		if ( !empty( $data['plugin_dir_url'] ) && is_string( $data['plugin_dir_url'] ) )
		{
			$this->plugin_dir_url = $data['plugin_dir_url'];
		}
	}


	public function update( $page )
	{
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
			$page2 = $this->hyper_pwa->get_sw_html();
			if ( preg_match( '/<script\b[^>]*>.+<\/script>/isU', $page2, $matches ) )
			{
				$head = $matches[0];
			}
		}

		if ( !empty( $head ) )
		{
			$page = preg_replace( '/(<head\b[^>]*>)/i', '${1}' . "\n" . $head, $page, 1 );
		}


		$page = preg_replace( '/<link\b[^>]* rel=(("manifest")|(\'manifest\'))[^>]*\s*?\/?>/iU', '', $page );
		$page = preg_replace( '/<meta\b[^>]* name=(("theme-color")|(\'theme-color\'))[^>]*\s*?\/?>/iU', '', $page );
		$page = preg_replace( '/<link\b[^>]* rel=(("apple-touch-icon")|(\'apple-touch-icon\'))[^>]*\s*?\/?>/iU', '', $page );

		$head = '<link rel="manifest" href="' . $this->home_url . '/hyper-pwa-manifest.json" />
<meta name="theme-color" content="#ffffff" />
<link rel="apple-touch-icon" href="' . $this->plugin_dir_url . 'manifest/logo-192.png" />';

		$page = preg_replace( '/<\/head>/i', $head . "\n" . '</head>', $page, 1 );


		if ( preg_match( '/<html\b[^>]* amp\b[^>]*>/i', $page ) )
		{
			$page = preg_replace( '/<amp-install-serviceworker\b[^>]+><\/amp-install-serviceworker>/i', '', $page );

			$body = '<amp-install-serviceworker src="' . $this->home_url . '/hyper-pwa-sw.js" data-iframe-src="' . $this->home_url . '/hyper-pwa-sw.html" layout="nodisplay"></amp-install-serviceworker>';

			$page = preg_replace( '/<\/body>/i', $body . "\n" . '</body>', $page, 1 );
		}

		return $page;
	}
}
