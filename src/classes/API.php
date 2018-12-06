<?php

use Contracts\APIContract;

/**
 * Load API and APP config.
 */
require BASEPATH."/config/api.php";
require BASEPATH."/config/app.php";

/**
 * Class ini digunakan untuk build JSON dan menjalankan API.
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
	 * @return string
	 */
	public static function validateToken()
	{
		if (isset($_SERVER["HTTP_AUTHORIZATION"])) {
			$a = explode("Bearer", $_SERVER["HTTP_AUTHORIZATION"], 2);
			if (count($a) === 2) {
				$a = json_decode(dencrypt(trim($a[1]), APP_KEY), true);
				if (isset($a["expired"], $a["code"])) {

					// If the token has been expired.
					if ($a["expired"] < time()) {
						error_api("Unauthorized: Token expired", 401);
						exit;
					}

					return $a["code"];
				}
			}
		}
		error_api("Unauthorized", 401);
		exit;
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
	public static function json001(string $status, $data): string
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