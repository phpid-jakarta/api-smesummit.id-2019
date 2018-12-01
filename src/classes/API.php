<?php

use Contracts\APIContract;

/**
 * Load API config.
 */
require BASEPATH."/config/api.php";

/**
 * Class ini digunakan untuk build JSON dan menjalankan API.
 *
 * @link https://en.wikipedia.org/wiki/Singleton_pattern
 *
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \
 */
final class API
{	
	/**
	 * @param string $api
	 * @return void
	 */
	public static function dispatch(string $api): void
	{
		$api = "\\API\\{$api}";
		$api = new $api();
		self::exec($api);
	}

	/**
	 * @param \Contracts\APIContract
	 * @return void
	 */
	private static function exec(APIContract $api): void
	{
		$api->run();
		exit;
	}

	/**
	 * @param string $status
	 * @param mixed  $data
	 */
	public static function json011(string $status, $data): string
	{
		return self::encode(
			[
				"status" => $status,
				"data" => $data
			]
		);
	}

	/**
	 * @param mixed $data
	 * @return string
	 */
	public static function encode($data): string
	{
		return json_encode($data, JSON_PARAMETER);
	}
}