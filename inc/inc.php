<?php
if ( !defined( 'ABSPATH' ) )
{
	exit;
}

class HyperPWAInc
{
	private $hyper_pwa = NULL;


	public function __construct( $hyper_pwa )
	{
		$this->hyper_pwa = $hyper_pwa;
	}

	public function __destruct()
	{
	}


	public function add_service_worker( $page, $host_dir, $manifest_logo_192_url, $page_type )
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
			$page2 = $this->hyper_pwa->get_service_worker_html();
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

		$head = '
<link rel="manifest" href="' . $host_dir . '/hyper-pwa-manifest.json" />
<meta name="theme-color" content="#ffffff" />
<link rel="apple-touch-icon" href="' . $host_dir . $manifest_logo_192_url . '" />
<meta name="hyper-pwa-page-type" content="' . $page_type . '" />';

		$page = preg_replace( '/<\/head>/i', $head . "\n" . '</head>', $page, 1 );


		if ( preg_match( '/<html\b[^>]* amp\b[^>]*>/i', $page ) )
		{
			$page = preg_replace( '/<amp-install-serviceworker\b[^>]+><\/amp-install-serviceworker>/i', '', $page );

			$body = '<amp-install-serviceworker src="' . $host_dir . '/hyper-pwa-service-worker.js" data-iframe-src="' . $host_dir . '/hyper-pwa-service-worker.html" layout="nodisplay"></amp-install-serviceworker>';

			$page = preg_replace( '/<\/body>/i', $body . "\n" . '</body>', $page, 1 );
		}

		return $page;
	}

	public function add_service_worker_unregister( $page )
	{
		$head = '';

		$page2 = $this->hyper_pwa->get_service_worker_unregister_html();
		if ( preg_match( '/<script\b[^>]*>.+<\/script>/isU', $page2, $matches ) )
		{
			$head = $matches[0];
		}

		if ( !empty( $head ) )
		{
			$page = preg_replace( '/(<head\b[^>]*>)/i', '${1}' . "\n" . $head, $page, 1 );
		}

		return $page;
	}
}
