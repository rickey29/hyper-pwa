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


	private function query( $url, $request, $transient )
	{
		$url = esc_url( $url );
		$response = wp_remote_get( $url, $request );

		$http_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $http_code )
		{
			return;
		}

		set_transient( $transient, $response, HYPER_PWA_TRANSIENT_EXPIRATION );

		$response = wp_remote_retrieve_body( $response );
		$response = json_decode( $response, TRUE );

		return $response;
	}

	private function query_flx( $transient, $routing, $request )
	{
		$response = get_transient( $transient );
		if ( FALSE !== $response )
		{
			$response = wp_remote_retrieve_body( $response );
			$response = json_decode( $response, TRUE );

			return $response;
		}


		$url = HYPER_PWA_FLX_SERVER_1 . $routing;
		$response = $this->query( $url, $request, $transient );
		if ( !empty( $response ) )
		{
			return $response;
		}


		if ( HYPER_PWA_FLX_SERVER_2 == HYPER_PWA_FLX_SERVER_1 )
		{
			return;
		}

		$url = HYPER_PWA_FLX_SERVER_2 . $routing;
		$response = $this->query( $url, $request, $transient );
		if ( !empty( $response ) )
		{
			return $response;
		}


		if ( HYPER_PWA_FLX_SERVER_3 == HYPER_PWA_FLX_SERVER_2 )
		{
			return;
		}

		$url = HYPER_PWA_FLX_SERVER_3 . $routing;
		$response = $this->query( $url, $request, $transient );
		if ( !empty( $response ) )
		{
			return $response;
		}

		return;
	}

	private function get( $body, $home_url, $transient, $routing )
	{
		$request = array(
			'body' => $body
		);

		if ( preg_match( '/^https:\/\/127\.0\.0\.1\//im', $home_url ) )
		{
			$request = array_merge( $request, array( 'sslverify' => FALSE ) );
		}

		$response = $this->query_flx( $transient, $routing, $request );
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


	public function get_manifest_json( $home_url, $data )
	{
		$body = array(
			'home_url' => $home_url,
			'data' => $data
		);

		$page = $this->get( $body, $home_url, HYPER_PWA_MANIFEST_JSON, HYPER_PWA_FLX_MANIFEST_JSON );

		return $page;
	}

	public function get_offline_html( $home_url )
	{
		$body = array(
			'home_url' => $home_url
		);

		$page = $this->get( $body, $home_url, HYPER_PWA_OFFLINE_HTML, HYPER_PWA_FLX_OFFLINE_HTML );

		return $page;
	}

	public function get_service_worker_html( $home_url )
	{
		$body = array(
			'home_url' => $home_url
		);

		$page = $this->get( $body, $home_url, HYPER_PWA_SERVICE_WORKER_HTML, HYPER_PWA_FLX_SERVICE_WORKER_HTML );

		return $page;
	}

	public function get_service_worker_js( $home_url )
	{
		$body = array(
			'home_url' => $home_url
		);

		$page = $this->get( $body, $home_url, HYPER_PWA_SERVICE_WORKER_JS, HYPER_PWA_FLX_SERVICE_WORKER_JS );

		return $page;
	}

	public function get_service_worker_unregister_html( $home_url )
	{
		$body = array(
			'home_url' => $home_url
		);

		$page = $this->get( $body, $home_url, HYPER_PWA_SERVICE_WORKER_UNREGISTER_HTML, HYPER_PWA_FLX_SERVICE_WORKER_UNREGISTER_HTML );

		return $page;
	}
}
