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


	public function add_service_worker( $page, $host_dir, $manifest_logo_192_url )
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
			$page2 = $this->hyper_pwa->retrieve_service_worker_html();
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

		$page_type = $this->get_page_type();

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

		$page2 = $this->hyper_pwa->retrieve_service_worker_unregister_html();
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
