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
			
			default:
				break;
		}
	}

	/**
	 * @return void
	 */
	private function getToken(): void
	{
		print API::json001(
			"success",
			[
				"token" => cencrypt(json_encode(
					[
						"expired" => (time()+3600),
						"code" => rstr(16)
					]
				), APP_KEY)
			]
		);
	}
}
