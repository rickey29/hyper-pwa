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


	public function base64_encode(&$data)
	{
		if ( is_scalar($data) )
		{
			if ( is_string($data) )
			{
				$data = strtr(base64_encode($data), '+/=', '-_,');
			}
			else
			{
				$data = base64_encode(strval($data));
			}
		}
		else
		{
			if ( empty($data) )
			{
				return;
			}

			foreach ( $data as $key => $value )
			{
				$this->base64_encode($data[$key]);
			}
		}
	}

	public function base64_decode(&$data)
	{
		if ( is_scalar($data) )
		{
			if ( ( $value = base64_decode(strtr($data, '-_,', '+/='), true) ) !== false )
			{
				$data = $value;
			}
		}
		else
		{
			if ( empty($data) )
			{
				return;
			}

			foreach ( $data as $key => $value )
			{
				$this->base64_decode($data[$key]);
			}
		}
	}


	private function curl($url, $request)
	{
		$curl = curl_init($url);

		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($request)));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
		curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$response = curl_exec($curl);

		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ( $status != 200 ) {
//			die("Error: call to URL $url failed with status $status, response $response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
			return;
		}

		curl_close($curl);

		return $response;
	}

	public function query($routing, $request)
	{
		$request = json_encode($request);

		$url = FLX_SERVER . $routing;
		$response = $this->curl($url, $request);
		if ( !empty($response) )
		{
			$response = json_decode($response, true);

			return $response;
		}


		if ( FLX_SERVER2 == FLX_SERVER )
		{
			return;
		}

		$url = FLX_SERVER2 . $routing;
		$response = $this->curl($url, $request);
		if ( !empty($response) )
		{
			$response = json_decode($response, true);

			return $response;
		}


		if ( FLX_SERVER3 == FLX_SERVER2 )
		{
			return;
		}

		$url = FLX_SERVER3 . $routing;
		$response = $this->curl($url, $request);
		if ( !empty($response) )
		{
			$response = json_decode($response, true);

			return $response;
		}

		return;
	}
}
