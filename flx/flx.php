<?php
if ( !defined( 'ABSPATH' ) )
{
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'cfg.php';

class HyperPWAFlx
{
	private $transient = '';
	private $routing = '';


	public function __construct()
	{
	}

	public function __destruct()
	{
	}


	private function query( $url, $request )
	{
		$url = esc_url( $url );
		$response = wp_remote_get( $url, $request );

		$http_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $http_code )
		{
			return;
		}

		set_transient( $this->transient, $response, HYPER_PWA_TRANSIENT_EXPIRATION );

		$response = wp_remote_retrieve_body( $response );
		$response = json_decode( $response, TRUE );

		return $response;
	}

	private function query_flx( $request )
	{
		$response = get_transient( $this->transient );
		if ( FALSE !== $response )
		{
			$response = wp_remote_retrieve_body( $response );
			$response = json_decode( $response, TRUE );

			return $response;
		}


		$url = HYPER_PWA_FLX_SERVER_1 . $this->routing;
		$response = $this->query( $url, $request );
		if ( !empty( $response ) )
		{
			return $response;
		}


		if ( HYPER_PWA_FLX_SERVER_2 == HYPER_PWA_FLX_SERVER_1 )
		{
			return;
		}

		$url = HYPER_PWA_FLX_SERVER_2 . $this->routing;
		$response = $this->query( $url, $request );
		if ( !empty( $response ) )
		{
			return $response;
		}


		if ( HYPER_PWA_FLX_SERVER_3 == HYPER_PWA_FLX_SERVER_2 )
		{
			return;
		}

		$url = HYPER_PWA_FLX_SERVER_3 . $this->routing;
		$response = $this->query( $url, $request );
		if ( !empty( $response ) )
		{
			return $response;
		}

		return;
	}

	private function retrieve( $body, $home_url )
	{
		$request = array(
			'body' => $body
		);

		if ( preg_match( '/^https:\/\/127\.0\.0\.1\//im', $home_url ) )
		{
			$request = array_merge( $request, array( 'sslverify' => FALSE ) );
		}

		$response = $this->query_flx( $request );
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


	public function retrieve_manifest_json( $home_url, $data )
	{
		$this->transient = HYPER_PWA_MANIFEST_JSON;
		$this->routing = HYPER_PWA_FLX_MANIFEST_JSON;

		$body = array(
			'home_url' => $home_url,
			'data' => $data
		);

		$page = $this->retrieve( $body, $home_url );

		return $page;
	}

	public function retrieve_offline_html( $home_url )
	{
		$this->transient = HYPER_PWA_OFFLINE_HTML;
		$this->routing = HYPER_PWA_FLX_OFFLINE_HTML;

		$body = array(
			'home_url' => $home_url
		);

		$page = $this->retrieve( $body, $home_url );

		return $page;
	}

	public function retrieve_service_worker_html( $home_url )
	{
		$this->transient = HYPER_PWA_SERVICE_WORKER_HTML;
		$this->routing = HYPER_PWA_FLX_SERVICE_WORKER_HTML;

		$body = array(
			'home_url' => $home_url
		);

		$page = $this->retrieve( $body, $home_url );

		return $page;
	}

	public function retrieve_service_worker_js( $home_url, $data )
	{
		$this->transient = HYPER_PWA_SERVICE_WORKER_JS;
		$this->routing = HYPER_PWA_FLX_SERVICE_WORKER_JS;

		$body = array(
			'home_url' => $home_url,
			'data' => $data
		);

		$page = $this->retrieve( $body, $home_url );

		return $page;
	}

	public function retrieve_service_worker_unregister_html( $home_url )
	{
		$this->transient = HYPER_PWA_SERVICE_WORKER_UNREGISTER_HTML;
		$this->routing = HYPER_PWA_FLX_SERVICE_WORKER_UNREGISTER_HTML;

		$body = array(
			'home_url' => $home_url
		);

		$page = $this->retrieve( $body, $home_url );

		return $page;
	}
}
