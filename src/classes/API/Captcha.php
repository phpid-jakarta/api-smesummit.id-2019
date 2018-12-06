<?php

namespace API;

use API;
use Error;
use Contracts\APIContract;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \API
 */
class Captcha implements APIContract
{
	/**
	 * @var string|array
	 */
	private $token;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		if (!isset($_GET["token"])) {
			error_api("Bad Request: `token` parameter is required", 400);
			return;
		}

		if (!is_string($_GET["token"])) {
			error_api("Bad Request: `token` parameter must be a string", 400);
			return;
		}

		$this->token = $_GET["token"];
	}
	
	/**
	 * @return void
	 */
	public function run(): void
	{
		$this->decryptToken();
		$hash = sha1($_GET["token"]);
		$filename = BASEPATH."/storage/captcha_images/{$this->token["expired"]}_{$this->token["code"]}_{$hash}.jpg";
		unset($now, $_GET);
		try {
			if (file_exists($filename) || makeCaptcha($this->token["code"], $filename)) {
				header("Content-Type: image/jpg");
				$handle = fopen($filename, "r");
				if (is_resource($handle)) {
					while (!feof($handle)) {
						print fread($handle, 1024);
						flush();
					}
					fclose($handle);
				} else {
					error_api("An error occured when generating captcha", 500);	
				}
				exit;
			} else {
				error_api("An error occured when generating captcha", 500);
				exit;
			}
		} catch (Error $e) {
			unset($e);
			error_api("Internal Server Error: {$e->getMessage()}");
			exit;
		}
	}

	/**
	 * @return void
	 */
	private function decryptToken(): void
	{
		$this->token = json_decode(dencrypt($this->token, APP_KEY), true);

		if ((!isset($this->token["expired"], $this->token["code"])) || 
			(!preg_match("/^[a-zA-Z0-9]$/", $this->token["code"]))) {
			error_log("Bad Request: Invalid token", 400);
			return;
		}

		if ($this->token["expired"] <= time()) {
			error_log("Unauthorized: Token Expired", 401);
			return;
		}
	}
}
