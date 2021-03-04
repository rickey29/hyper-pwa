<?php
if ( !defined( 'ABSPATH' ) )
{
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'cfg.php';

class HyperPWAFlx
{
	public function __construct()
	{
	}

	public function __destruct()
	{
	}


	private function query( $transient, $routing, $request )
	{
		$response = get_transient( $transient );
		if ( FALSE !== $response )
		{
			$response = wp_remote_retrieve_body( $response );
			$response = json_decode( $response, TRUE );

			return $response;
		}


		$url = HYPER_PWA_FLX_SERVER_1 . $routing;
		$url = esc_url( $url );
		$response = wp_remote_get( $url, $request );

		$http_code = wp_remote_retrieve_response_code( $response );
		if ( 200 === $http_code )
		{
			set_transient( $transient, $response, DAY_IN_SECONDS );

			$response = wp_remote_retrieve_body( $response );
			$response = json_decode( $response, TRUE );

			return $response;
		}


		if ( HYPER_PWA_FLX_SERVER_2 == HYPER_PWA_FLX_SERVER_1 )
		{
			return;
		}

		$url = HYPER_PWA_FLX_SERVER_2 . $routing;
		$url = esc_url( $url );
		$response = wp_remote_get( $url, $request );

		$http_code = wp_remote_retrieve_response_code( $response );
		if ( 200 === $http_code )
		{
			set_transient( $transient, $response, DAY_IN_SECONDS );

			$response = wp_remote_retrieve_body( $response );
			$response = json_decode( $response, TRUE );

			return $response;
		}


		if ( HYPER_PWA_FLX_SERVER_3 == HYPER_PWA_FLX_SERVER_2 )
		{
			return;
		}

		$url = HYPER_PWA_FLX_SERVER_3 . $routing;
		$url = esc_url( $url );
		$response = wp_remote_get( $url, $request );

		$http_code = wp_remote_retrieve_response_code( $response );
		if ( 200 === $http_code )
		{
			set_transient( $transient, $response, DAY_IN_SECONDS );

			$response = wp_remote_retrieve_body( $response );
			$response = json_decode( $response, TRUE );

			return $response;
		}

		return;
	}


	public function get_manifest_webmanifest( $home_url, $data )
	{
		$body = array(
			'home_url' => $home_url,
			'data' => $data
		);

		$request = array(
			'body' => $body
		);

		if ( preg_match( '/^https:\/\/127\.0\.0\.1\//im', $home_url ) )
		{
			$request = array_merge( $request, array( 'sslverify' => FALSE ) );
		}

		$response = $this->query( 'manifest_webmanifest', HYPER_PWA_FLX_MANIFEST_WEBMANIFEST, $request );
		if ( empty( $response ) )
		{
			return;
		}

		if ( empty( $response['page'] ) || !is_string( $response['page'] ) )
		{
			return;
		}
		$page = $response['page'];

		return $page;
	}

	public function get_sw_html( $home_url )
	{
		$body = array(
			'home_url' => $home_url
		);

		$request = array(
			'body' => $body
		);

		if ( preg_match( '/^https:\/\/127\.0\.0\.1\//im', $home_url ) )
		{
			$request = array_merge( $request, array( 'sslverify' => FALSE ) );
		}

		$response = $this->query( 'hyper_pwa_sw_html', HYPER_PWA_FLX_SW_HTML, $request );
		if ( empty( $response ) )
		{
			return;
		}

		if ( empty( $response['page'] ) || !is_string( $response['page'] ) )
		{
			return;
		}
		$page = $response['page'];

		return $page;
	}

	public function get_sw_js( $home_url, $data )
	{
		$body = array(
			'home_url' => $home_url,
			'data' => $data
		);

		$request = array(
			'body' => $body
		);

		if ( preg_match( '/^https:\/\/127\.0\.0\.1\//im', $home_url ) )
		{
			$request = array_merge( $request, array( 'sslverify' => FALSE ) );
		}

		$response = $this->query( 'hyper_pwa_sw_js', HYPER_PWA_FLX_SW_JS, $request );
		if ( empty( $response ) )
		{
			return;
		}

		if ( empty( $response['page'] ) || !is_string( $response['page'] ) )
		{
			return;
		}
		$page = $response['page'];

		return $page;
	}
}
