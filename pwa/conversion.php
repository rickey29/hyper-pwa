<?php
if ( !defined('ABSPATH') )
{
	exit;
}

class HyperPWAConversion
{
	public function __construct()
	{
	}

	public function __destruct()
	{
	}


	public function convert($page, $home_url, $data)
	{
		require_once plugin_dir_path(__FILE__) . 'transcoding.php';

		$transcoding = new HyperPWATranscoding();

		$transcoding->init($home_url, $data);

		$page = $transcoding->transcode_html($page);

		$page = $transcoding->transcode_head($page);

		return $page;
	}
}
