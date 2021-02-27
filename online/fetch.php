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


	/*
		Input:
			$data = array(
				'plugin_dir' => $this->plugin_dir -- the directory of this plugin
			);

		Output:
			$page -- the PWA Service Workers JavaScript file FlexPlat generated
	*/
	public function fetch($data)
	{
		$flx = new HyperPWAFlx();

		$body = array(
			'data' => $data
		);

		$request = array(
			'body' => $body
		);

		$response = $flx->query(HYPER_PWA_FLX, $request);
		if ( empty($response) )
		{
			return;
		}

		if ( empty($response['page']) || !is_string($response['page']) )
		{
			return;
		}
		$page = $response['page'];

		return $page;
	}
}
