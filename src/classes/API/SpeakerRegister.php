<?php

namespace API;

use DB;
use API;
use PDOException;
use Contracts\APIContract;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com> https://www.facebook.com/ammarfaizi2
 * @license MIT
 * @package \API
 */
class SpeakerRegister implements APIContract
{
	/**
	 * @var string
	 */
	private $action;

	/**
	 * @var string
	 */
	private $captcha;

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
		if ($_SERVER["REQUEST_METHOD"] !== "POST") {
			error_api("Method not allowed", 405);
		}
		
		$this->captcha = API::validateToken();

		// Validate input
		$i = json_decode(file_get_contents("php://input"), true);
		if (!is_array($i)) {
			error_api("Invalid request body");
			return;
		}
		$this->validateSubmitInput($i);
		$this->save($i);
	}

	/**
	 * @return void
	 */
	private function save(array &$i): void
	{
		try {
			$pdo = DB::pdo();
			$st = $pdo->prepare(
				"INSERT INTO `speakers` (`name`, `company_name`, `position`, `email`, `photo`, `last_education`, `experience`, `phone`, `sector`, `topic`, `created_at`) VALUES (:name,:company_name,:position,:email,:photo,:last_education,:experience,:phone,:sector,:topic,:created_at);"
			);
			$st->execute(
				[
					":name" => $i["name"],
					":company_name" => $i["company_name"],
					":position" => $i["position"],
					":email" => $i["email"],
					":photo" => $i["photo"],
					":last_education" => $i["last_education"],
					":experience" => $i["experience"],
					":phone" => $i["phone"],
					":sector" => $i["sector"],
					":topic" => $i["topic"],
					":created_at" => date("Y-m-d H:i:s")
				]
			);
			print API::json001("success",
				[
					"message" => "register_success"
				]
			);
		} catch (PDOException $e) {
			// Close PDO connection.
			$st = $pdo = null;
			
			error_api("Internal Server Error: {$e->getMessage()}", 500);

			unset($e, $st, $pdo, $i);
			exit;
		}

		// Close PDO connection.
		$st = $pdo = null;
		unset($st, $pdo, $i);
	}

	/**
	 * @param array &$i
	 * @return void
	 */
	private function validateSubmitInput(array &$i): void
	{
		$m = "Bad Request:";
		$required = [
			"name",
			"company_name",
			"position",
			"email",
			"photo",
			"last_education",
			"experience",
			"phone",
			"sector",
			"topic",
			"captcha"
		];

		foreach ($required as $v) {
			if (!isset($i[$v])) {
				error_api("{$m} Field required: {$v}", 400);
				return;
			}
			if (!is_string($i[$v])) {
				error_api("{$m} Field `{$v}` must be a string", 400);
				return;
			}

			$i[$v] = trim($i[$v]);
		}

		if ($i["captcha"] !== $this->captcha) {
			error_api("{$m} Invalid captcha response", 400);
			return;
		}

		unset($required, $v);

		if (!preg_match("/^[a-z\.\'\s]{3,255}$/i", $i["name"])) {
			error_api("{$m} Field `name` must be a valid person", 400);
			return;
		}

		if (!preg_match("/^[a-z0-9\-\.\'\s]{3,255}$/i", $i["company_name"])) {
			error_api("{$m} Field `company_name` must be a valid company", 400);
			return;
		}

		// $positions_enum = json_decode(file_get_contents(BASEPATH."/public/positions.json"), true);
		
		// if (!is_array($positions_enum)) {
		// 	error_api(
		// 		sprintf("Positions enum cannot be loaded properly %s", 
		// 			BASEPATH."/public/positions.json"
		// 		), 
		// 		500
		// 	);
		// 	return;
		// }

		// if (!in_array($i["position"], $positions_enum)) {
		// 	error_api("{$m} Field `position` must be a valid position", 400);
		// 	return;
		// }



		// unset($positions_enum);

		$c = strlen($i["position"]);

		if ($c < 4) {
			error_api("{$m} `position` field is not a valid position (too short)");
		}

		if ($c > 100) {
			error_api("{$m} `position` field is not a valid position (too long)", 400);
		}

		if (!filter_var($i["email"], FILTER_VALIDATE_EMAIL)) {
			error_api("{$m} \"{$i["email"]}\" is not a valid email address", 400);
			return;
		}

		$c = strlen($i["experience"]);

		if ($c < 20) {
			error_api("{$m} `experience` is too short. Please provide a description at least 20 bytes.", 400);
			return;
		}

		if ($c >= 1024) {
			error_api("{$m} `experience` is too long. Please provide a description with size less than 1024 bytes.", 400);
			return;
		}

		if (preg_match("/^[0-9\-\+]*$/", $i["phone"])) {
			if (!preg_match("/^[0\+]\d{4,13}$/", str_replace("-", "", $i["phone"]))) {
				error_api("{$m} Invalid phone number.", 400);	
				return;
			}
		} else {
			if (!preg_match("/^\@/", $i["phone"])) {
				error_api("{$m} Invalid telegram username: Telegram username must be started with '@' or enter your phone number instead", 400);
				return;
			}

			if (!preg_match("/^\@[a-z0-9][a-z0-9\_]{3,25}[a-z0-9]$/i", $i["phone"])) {
				error_api("{$m} Invalid telegram username", 400);
				return;
			}
		}

		if (!preg_match("/^[a-z0-9\-\.\'\s\&\_\/]{3,255}$/i", $i["sector"])) {
			error_api("{$m} Field `sector` must be a valid sector", 400);
			return;
		}

		$c = strlen($i["topic"]);

		if ($c < 5) {
			error_api("{$m} `topic` is too short. Please provide a topic at least 5 bytes.", 400);
			return;
		}

		if ($c >= 1024) {
			error_api("{$m} `topic` is too long. Please provide a topic with size less than 1024 bytes.", 400);
			return;
		}

		unset($c, $i);
		return;
	}

	/**
	 * @return void
	 */
	private function getToken(): void
	{
		$expired = time()+3600;

		// By using this token, we don't need any session which saved at the server side.
		print API::json001(
			"success",
			[
				// Encrypted expired time and random code 32 bytes.
				"token" => cencrypt(json_encode(
					[
						"expired" => $expired,
						"code" => rstr(6, "1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM")
					]
				), APP_KEY),

				// Show expired time.
				"expired" => $expired
			]
		);
	}
}
