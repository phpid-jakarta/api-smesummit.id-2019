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
class PaymentConfirmation implements APIContract
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
