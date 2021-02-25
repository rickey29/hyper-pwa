<?php
if ( !defined('ABSPATH') )
{
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'cfg.php';
require_once plugin_dir_path( __FILE__ ) . 'flx.php';

class HyperPWAFetch
{
	public function __construct()
	{
	}

	public function __destruct()
	{
	}


	public function fetch($data)
	{
		$flx = new HyperPWAFlx();

		$request = array(
			'data' => $data
		);

		$response = $flx->query(FLX_PWA, $request);
		if ( empty($response) )
		{
			return '';
		}

		if ( !isset($response['page']) || !is_string($response['page']) )
		{
			return '';
		}
		$page = $response['page'];

		$flx->base64_decode($page);

		return $page;
	}
}
