<?php
if ( !defined('ABSPATH') )
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


	/*
		HYPER_PWA_FLX_SERVER, HYPER_PWA_FLX_SERVER_2 and HYPER_PWA_FLX_SERVER_3 have the same code/service.
		There are two reasons why I set up three servers, two of them are as the redundant backup:
			1. If one of these servers is Out Of Service, the users can still get service from the other two ones;
			2. When I update the software of one server, the users can still get service from the other two ones.
	*/
	public function query($routing, $request)
	{
		$response = get_transient( 'hyper_pwa_sw_js' );
		if ( false !== $response )
		{
			$response = wp_remote_retrieve_body( $response );
			$response = json_decode( $response, true );

			return $response;
		}


		$url = HYPER_PWA_FLX_SERVER . $routing;
		$response = wp_remote_get( $url, $request );

		$http_code = wp_remote_retrieve_response_code( $response );
		if ( 200 === $http_code )
		{
			set_transient( 'hyper_pwa_sw_js', $response, DAY_IN_SECONDS );

			$response = wp_remote_retrieve_body( $response );
			$response = json_decode( $response, true );

			return $response;
		}


		if ( HYPER_PWA_FLX_SERVER_2 == HYPER_PWA_FLX_SERVER )
		{
			return;
		}

		$url = HYPER_PWA_FLX_SERVER_2 . $routing;
		$response = wp_remote_get( $url, $request );

		$http_code = wp_remote_retrieve_response_code( $response );
		if ( 200 === $http_code )
		{
			set_transient( 'hyper_pwa_sw_js', $response, DAY_IN_SECONDS );

			$response = wp_remote_retrieve_body( $response );
			$response = json_decode( $response, true );

			return $response;
		}


		if ( HYPER_PWA_FLX_SERVER_3 == HYPER_PWA_FLX_SERVER_2 )
		{
			return;
		}

		$url = HYPER_PWA_FLX_SERVER_3 . $routing;
		$response = wp_remote_get( $url, $request );

		$http_code = wp_remote_retrieve_response_code( $response );
		if ( 200 === $http_code )
		{
			set_transient( 'hyper_pwa_sw_js', $response, DAY_IN_SECONDS );

			$response = wp_remote_retrieve_body( $response );
			$response = json_decode( $response, true );

			return $response;
		}

		return;
	}
}
