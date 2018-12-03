<?php

namespace API;

use DB;
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
		$pdo = DB::pdo();
		$st = $pdo->prepare(
			"INSERT INTO `participants` (`name`, `company_name`, `position`, `company_sector`, `email`, `phone`, `problem_desc`, `created_at`) VALUES (:name, :company_name, :position, :company_sector, :email, :phone, :problem_desc, :created_at);"
		);
		$st->execute(
			[
				":name" => $i["name"],
				":company_name" => $i["company_name"],
				":position" => $i["position"],
				":company_sector" => $i["company_sector"],
				":email" => $i["email"],
				":phone" => $i["phone"],
				":problem_desc" => $i["problem_desc"],
				":created_at" => date("Y-m-d H:i:s")
			]
		);

		// Close PDO connection.
		$st = $pdo = null;
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
			"company_sector",
			"email",
			"phone",
			"problem_desc"
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

		unset($required, $v);

		if (!preg_match("/^[a-z\.\'\s]{3,}$/i", $i["name"])) {
			error_api("{$m} Field `name` must be a valid person", 400);
			return;
		}

		if (!preg_match("/^[a-z0-9\-\.\'\s]{3,}$/i", $i["company_name"])) {
			error_api("{$m} Field `company_name` must be a valid company", 400);
			return;
		}

		if (!filter_var($i["email"], FILTER_VALIDATE_EMAIL)) {
			error_api("{$m} \"{$i["email"]}\" is not a valid email address", 400);
			return;
		}

		if (!preg_match("/^[0\+]\d{4,13}$/", $i["phone"])) {
			error_api("{$m} Invalid phone number", 400);	
			return;
		}

		$c = strlen($i["problem_desc"]);

		if ($c < 20) {
			error_api("{$m} `problem_desc` is too short. Please provide a description at least 20 bytes.", 400);
			return;
		}

		if ($c >= 200) {
			error_api("{$m} `problem_desc` is too long. Please provide a description with size less than 200 bytes.", 400);
			return;
		}

		unset($c);
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
