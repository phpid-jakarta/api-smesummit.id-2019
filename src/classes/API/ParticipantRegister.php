<?php

namespace API;

use API;
use Contracts\APIContract;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \API
 */
class ParticipantRegister implements APIContract
{
	/**
	 * @var string
	 */
	private $action;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		if (!isset($_GET["action"])) {
			error_api("Bad Request: Invalid action", 400);
		}

		$this->action = $_GET["action"];
	}

	/**
	 * @return void
	 */
	public function sendHeaders(): void
	{

	}

	/**
	 * @return void
	 */
	public function run(): void
	{
		switch ($this->action) {
			case "get_token":
				$this->getToken();
				break;
			case "submit":
				$this->submit();
				break;
			default:
				break;
		}
	}

	/**
	 * @return void
	 */
	private function submit(): void
	{
		API::validateToken();

		// Validate input
		$i = json_decode(file_get_contents("php://input"), true);
		$this->validateSubmitInput($i);
	}

	/**
	 * @return void
	 */
	private function validateSubmitInput($i): void
	{
		$m = "Bad Request:";
		$required = [
			"name",
			"company_name",
			"position",
			"company_sector",
			"email",
			"phone",
			"problem_desc"
		];

		foreach ($required as $v) {
			if (!isset($i[$v])) {
				error_api("{$m} Field required: {$v}", 400);
			}
			if (!is_string($i[$v])) {
				error_api("{$m} Field `{$v}` must be a string", 400);	
			}

			$i[$v] = trim($i[$v]);
		}

		if (!preg_match("/^[a-z\.\'\s]$/i", $i["name"])) {
			error_api("{$m} Field `name` must be a valid person");
		}

		if (!preg_match("/^[a-z0-9\-\.\'\s]$/i", $i["company_name"])) {
			error_api("{$m} Field `company_name` must be a valid company");
		}

		
	}

	/**
	 * @return void
	 */
	private function getToken(): void
	{
		$expired = time()+3600;
		print API::json001(
			"success",
			[
				"token" => cencrypt(json_encode(
					[
						"expired" => $expired,
						"code" => rstr(32)
					]
				), APP_KEY),
				"expired" => $expired
			]
		);
	}
}
